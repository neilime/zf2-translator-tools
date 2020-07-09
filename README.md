TranslatorTools
=======

__⚠️ This module is for Zend Framework 2, it is deprecated ⚠️__ 

[![Build Status](https://travis-ci.org/neilime/zf2-translator-tools.png?branch=master)](https://travis-ci.org/neilime/zf2-translator-tools)

Created by Neilime

Introduction
------------

__TranslatorTools__ is an utility module for maintaining Zend Framework 2 translations files.

P.S. If You wish to help me with this project - You are welcome :)

Requirements
------------

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)

Installation
------------

### Main Setup

#### By cloning project

2. Clone this project into your `./vendor/` directory.

#### With composer

1. Add this project in your composer.json:

    ```json
    "require": {
        "neilime/zf2-translator-tools": "dev-master"
    }
    ```
2. Now tell composer to download TranslatorTools by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'TranslatorTools',
        ),
        // ...
    );
    ```
    
# How to use __TranslatorTools__

## Features

    Remove useless translations

## Usage

### Remove useless translations

    php public/index.php removeTranslations

## Configuration  
