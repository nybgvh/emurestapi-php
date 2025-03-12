# EMu REST API PHP Package

This package provides a simple PHP library for interacting with the EMu REST API (https://help.emu.axiell.com/emurestapi/latest/). It includes classes for authentication (`Auth`), record retrieval (`Retrieve`), and advanced search (`Search`). Designed for compatibility with PHP 5.2+ (with JSON support in 5.2+).

## Installation

1. Download or clone this repository.
2. Place the package in your PHP project.
3. Update the `src/env.php` file with your EMu API configuration.
4. **(Optional)** Run the tests:
   - Change directory to the `tests` folder.
   - Update the test files with your own test data if needed.
   - Run the test scripts from the command line, e.g.:
     ```bash
     cd tests
     php RetrieveTest.php
     ```

## Directory Structure

`src/`:  
  All core classes and configuration files are placed here. This directory is intended to be included in your projects when you need to interact with the EMu REST API.

`tests/`: 
  Contains simple PHP test scripts for each class. These tests use basic assertions to help you verify that the API interactions work as expected. You can run these tests from the command line to ensure everything is functioning correctly in your environment.


