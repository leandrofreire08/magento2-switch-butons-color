# Magento 2 Module Freire_HrefLang

## Functionalities

* CLI tool to change all buttons color by store

## Usage

### Installation

Install this module into Magento 2 via composer:

    git clone https://github.com/leandrofreire08/magento2-switch-butons-color.git app/code/Freire/HrefLang
    bin/magento module:enable Freire_SwitchButtonsColor
    bin/magento setup:upgrade

### Using
    php bin/magento freire:swith-button-color <hex_color> <store_id>
    
    i.e: php bin/magento freire:swith-button-color 00000 1

### Uninstall

    bin/magento module:uninstall Freire_SwitchButtonsColor

## Author

Leandro Freire
