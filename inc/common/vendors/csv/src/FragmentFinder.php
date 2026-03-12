<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Csv;

use function array_filter;
use function array_map;
use function array_reduce;
use function count;
use function explode;
use function filter_var;
use function preg_match;
use function range;

use const FILTER_VALIDATE_INT;

/**
 * @phpstan-type selection array{selection:string, start:int<-1, max>, end:?int, length:int, columns:array<int>}
 */
class FragmentFinder {

	private const REGEXP_URI_FRAGMENT           = ',^(?<type>row|cell|col)=(?<selections>.*)$,i';
	private const REGEXP_ROWS_COLUMNS_SELECTION = '/^(?<start>\d+)(-(?<end>\d+|\*))?$/';
	private const REGEXP_CELLS_SELECTION        = '/^(?<csr>\d+),(?<csc>\d+)(-(?<end>((?<cer>\d+),(?<cec>\d+))|\*))?$/';
	private const TYPE_ROW                      = 'row';
	private const TYPE_COLUMN                   = 'col';
	private const TYPE_UNKNOWN                  = 'unknown';

	public static function create(): self {
		return new self();
	}

	/**
	 * @throws SyntaxError
	 *
	 * @return iterable<int, TabularDataReader>
	 */
	public function findAll( string $expression, TabularDataReader $tabularDataReader ): iterable {
		return $this->find( $this->parseExpression( $expression, $tabularDataReader ), $tabularDataReader );
	}

	/**
	 * @throws SyntaxError
	 */
	public function findFirst( string $expression, TabularDataReader $tabularDataReader ): ?TabularDataReader {
		$fragment = $this->find( $this->parseExpression( $expression, $tabularDataReader ), $tabularDataReader )[0];

		return match ( array() ) {
			$fragment->first() => null,
			default => $fragment,
		};
	}

	/**
	 * @throws SyntaxError
	 * @throws FragmentNotFound if the expression can not be parsed
	 */
	public function findFirstOrFail( string $expression, TabularDataReader $tabularDataReader ): TabularDataReader {
		$parsedExpression = $this->parseExpression( $expression, $tabularDataReader );
		if ( array() !== array_filter( $parsedExpression['selections'], fn ( array $selection ) => -1 === $selection['start'] ) ) {
			throw new FragmentNotFound( 'The expression `' . $expression . '` contains an invalid or an unsupported selection for the tabular data.' );
		}

		$fragment = $this->find( $parsedExpression, $tabularDataReader )[0];

		return match ( array() ) {
			$fragment->first() => throw new FragmentNotFound( 'No fragment found in the tabular data with the expression `' . $expression . '`.' ),
			default => $fragment,
		};
	}

	/**
	 * @param array{type:string, selections:non-empty-array<selection>} $parsedExpression
	 *
	 * @throws SyntaxError
	 *
	 * @return array<int, TabularDataReader>
	 */
	private function find( array $parsedExpression, TabularDataReader $tabularDataReader ): array {
		['type' => $type, 'selections' => $selections] = $parsedExpression;

		$selections = array_filter( $selections, fn ( array $selection ) => -1 !== $selection['start'] );
		if ( array() === $selections ) {
			return array( ResultSet::createFromRecords() );
		}

		if ( self::TYPE_ROW === $type ) {
			$rowFilter = fn ( array $record, int $offset ): bool => array() !== array_filter(
				$selections,
				fn ( array $selection ) =>
						$offset >= $selection['start'] &&
						( null === $selection['end'] || $offset <= $selection['end'] )
			);

			return array( Statement::create()->where( $rowFilter )->process( $tabularDataReader ) );
		}

		if ( self::TYPE_COLUMN === $type ) {
			$columns = array_reduce(
				$selections,
				fn ( array $columns, array $selection ) => array( ...$columns, ...$selection['columns'] ),
				array()
			);

			return array(
				match ( array() ) {
								$columns => ResultSet::createFromRecords(),
								default => Statement::create()->select( ...$columns )->process( $tabularDataReader ),
				},
			);
		}

		return array_map(
			fn ( array $selection ) => Statement::create()
				->select( ...$selection['columns'] )
				->offset( $selection['start'] )
				->limit( $selection['length'] )
				->process( $tabularDataReader ),
			$selections
		);
	}

	/**
	 * @return array{type:string, selections:non-empty-array<selection>}
	 */
	private function parseExpression( string $expression, TabularDataReader $tabularDataReader ): array {
		if ( 1 !== preg_match( self::REGEXP_URI_FRAGMENT, $expression, $matches ) ) {
			return array(
				'type'       => self::TYPE_UNKNOWN,
				'selections' => array(
					array(
						'selection' => $expression,
						'start'     => -1,
						'end'       => null,
						'length'    => -1,
						'columns'   => array(),
					),
				),
			);
		}

		$type = strtolower( $matches['type'] );

		/** @var non-empty-array<selection> $res */
		$res = array_reduce(
			explode( ';', $matches['selections'] ),
			fn ( array $selections, string $selection ): array => array(
				...$selections,
				match ( $type ) {
								self::TYPE_ROW => $this->parseRowSelection( $selection ),
								self::TYPE_COLUMN => $this->parseColumnSelection( $selection, $tabularDataReader ),
								default => $this->parseCellSelection( $selection, $tabularDataReader ),
							},
			),
			array()
		);

		return array(
			'type'       => $type,
			'selections' => $res,
		);
	}

	/**
	 * @return selection
	 */
	private function parseRowSelection( string $selection ): array {
		[$start, $end] = $this->parseRowColumnSelection( $selection );

		return match ( true ) {
			-1 === $start,
			null === $end => array(
				'selection' => $selection,
				'start'     => $start,
				'end'       => $start,
				'length'    => 1,
				'columns'   => array(),
			),
			'*' === $end => array(
				'selection' => $selection,
				'start'     => $start,
				'end'       => null,
				'length'    => -1,
				'columns'   => array(),
			),
			default => array(
				'selection' => $selection,
				'start'     => $start,
				'end'       => $end,
				'length'    => $end - $start + 1,
				'columns'   => array(),
			),
		};
	}

	/**
	 * @return selection
	 */
	private function parseColumnSelection( string $selection, TabularDataReader $tabularDataReader ): array {
		[$start, $end] = $this->parseRowColumnSelection( $selection );
		$header        = $tabularDataReader->getHeader();
		if ( array() === $header ) {
			$header = $tabularDataReader->first();
		}

		$nbColumns = count( $header );

		return match ( true ) {
			-1 === $start,
			$start >= $nbColumns => array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => -1,
				'columns'   => array(),
			),
			null === $end => array(
				'selection' => $selection,
				'start'     => 0,
				'end'       => null,
				'length'    => -1,
				'columns'   => array( $start ),
			),
			'*' === $end,
			$end > ( $nbColumns - 1 ) => array(
				'selection' => $selection,
				'start'     => 0,
				'end'       => null,
				'length'    => -1,
				'columns'   => range( $start, $nbColumns - 1 ),
			),
			default => array(
				'selection' => $selection,
				'start'     => 0,
				'end'       => $end,
				'length'    => -1,
				'columns'   => range( $start, $end ),
			),
		};
	}

	/**
	 * @return array{int<-1, max>, int|null|'*'}
	 */
	private function parseRowColumnSelection( string $selection ): array {
		if ( 1 !== preg_match( self::REGEXP_ROWS_COLUMNS_SELECTION, $selection, $found ) ) {
			return array( -1, 0 );
		}

		$start = $found['start'];
		$end   = $found['end'] ?? null;
		$start = filter_var( $start, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( false === $start ) {
			return array( -1, 0 );
		}
		--$start;

		if ( null === $end || '*' === $end ) {
			return array( $start, $end );
		}

		$end = filter_var( $end, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( false === $end ) {
			return array( -1, 0 );
		}
		--$end;

		if ( $end <= $start ) {
			return array( -1, 0 );
		}

		return array( $start, $end );
	}

	/**
	 * @return selection
	 */
	private function parseCellSelection( string $selection, TabularDataReader $tabularDataReader ): array {
		if ( 1 !== preg_match( self::REGEXP_CELLS_SELECTION, $selection, $found ) ) {
			return array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => 1,
				'columns'   => array(),
			);
		}

		$cellStartRow = filter_var( $found['csr'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		$cellStartCol = filter_var( $found['csc'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		if ( false === $cellStartRow || false === $cellStartCol ) {
			return array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => 1,
				'columns'   => array(),
			);
		}

		--$cellStartRow;
		--$cellStartCol;

		$header = $tabularDataReader->getHeader();
		if ( array() === $header ) {
			$header = $tabularDataReader->first();
		}

		$nbColumns = count( $header );

		if ( $cellStartCol > $nbColumns - 1 ) {
			return array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => 1,
				'columns'   => array(),
			);
		}

		$cellEnd = $found['end'] ?? null;
		if ( null === $cellEnd ) {
			return array(
				'selection' => $selection,
				'start'     => $cellStartRow,
				'end'       => null,
				'length'    => 1,
				'columns'   => array( $cellStartCol ),
			);
		}

		if ( '*' === $cellEnd ) {
			return array(
				'selection' => $selection,
				'start'     => $cellStartRow,
				'end'       => null,
				'length'    => -1,
				'columns'   => range( $cellStartCol, $nbColumns - 1 ),
			);
		}

		$cellEndRow = filter_var( $found['cer'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
		$cellEndCol = filter_var( $found['cec'], FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );

		if ( false === $cellEndRow || false === $cellEndCol ) {
			return array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => 1,
				'columns'   => array(),
			);
		}

		--$cellEndRow;
		--$cellEndCol;

		if ( $cellEndRow < $cellStartRow || $cellEndCol < $cellStartCol ) {
			return array(
				'selection' => $selection,
				'start'     => -1,
				'end'       => null,
				'length'    => 1,
				'columns'   => array(),
			);
		}

		return array(
			'selection' => $selection,
			'start'     => $cellStartRow,
			'end'       => $cellEndRow,
			'length'    => $cellEndRow - $cellStartRow + 1,
			'columns'   => range( $cellStartCol, ( $cellEndCol > $nbColumns - 1 ) ? $nbColumns - 1 : $cellEndCol ),
		);
	}
}
