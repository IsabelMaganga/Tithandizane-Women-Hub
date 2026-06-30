<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::statement('DROP TABLE IF EXISTS users_new;');

            DB::statement(
                'CREATE TABLE users_new (
                    id integer primary key autoincrement not null,
                    name varchar not null,
                    email varchar not null,
                    password varchar not null,
                    role varchar check (role in (\'user\', \'mentor\', \'admin\')) not null default \'user\',
                    phone varchar,
                    location varchar,
                    photo varchar,
                    expertise text,
                    bio text,
                    notes text,
                    status varchar check (status in (\'active\', \'inactive\', \'banned\')) not null default \'active\',
                    availability varchar,
                    linkedin_url varchar,
                    twitter_url varchar,
                    website_url varchar,
                    expertise_area varchar,
                    is_available tinyint(1) not null default 1,
                    available_days text,
                    available_time_start varchar,
                    available_time_end varchar,
                    remember_token varchar,
                    created_at datetime,
                    updated_at datetime,
                    is_active tinyint(1) not null default 1,
                    specialization varchar,
                    last_password_updated_at datetime
                )'
            );

            DB::statement('INSERT INTO users_new SELECT * FROM users');
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_new RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive', 'banned') NOT NULL DEFAULT 'active';");
        }
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::statement('DROP TABLE IF EXISTS users_old;');

            DB::statement(
                'CREATE TABLE users_old (
                    id integer primary key autoincrement not null,
                    name varchar not null,
                    email varchar not null,
                    password varchar not null,
                    role varchar check (role in (\'user\', \'mentor\', \'admin\')) not null default \'user\',
                    phone varchar,
                    location varchar,
                    photo varchar,
                    expertise text,
                    bio text,
                    notes text,
                    status varchar check (status in (\'active\', \'inactive\')) not null default \'active\',
                    availability varchar,
                    linkedin_url varchar,
                    twitter_url varchar,
                    website_url varchar,
                    expertise_area varchar,
                    is_available tinyint(1) not null default 1,
                    available_days text,
                    available_time_start varchar,
                    available_time_end varchar,
                    remember_token varchar,
                    created_at datetime,
                    updated_at datetime,
                    is_active tinyint(1) not null default 1,
                    specialization varchar,
                    last_password_updated_at datetime
                )'
            );

            DB::statement("INSERT INTO users_old SELECT id, name, email, password, role, phone, location, photo, expertise, bio, notes, 
                CASE WHEN status = 'banned' THEN 'inactive' ELSE status END, availability, linkedin_url, twitter_url, website_url, expertise_area,
                is_available, available_days, available_time_start, available_time_end, remember_token, created_at, updated_at, is_active,
                specialization, last_password_updated_at
                FROM users");
            DB::statement('DROP TABLE users');
            DB::statement('ALTER TABLE users_old RENAME TO users');
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';");
        }
    }
};
