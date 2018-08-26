<?php

namespace User_Login_History\Inc\Common\Interfaces;

interface IAdminSettings {

    public function admin_init();

    public function set_sections($sections);

    public function set_fields($fields);

    public function show_navigation();

    public function show_forms();
}
