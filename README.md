# OE Statistics Module

## Description

A module for logging and displaying statistics for OXID eShop Community and Professional Edition (formerly part of the core)

### Requirements

* OXID eShop v6.*

## Installation

Please proceed with one of the following ways to install the module:

### Module installation via composer

In order to install the module via composer, run the following commands in commandline of your shop base directory 
(where the shop's composer.json file resides).

```
composer config repositories.oxid-esales/statistics-module vcs https://github.com/OXIDprojects/statistics-module
composer require oxid-esales/statistics-module:dev-master
```

### Module installation via repository cloning

Clone the module to your OXID eShop **modules/oe/** directory:
```
git clone https://github.com/OXIDprojects/statistics-module.git statistics
```

### Module installation from zip package

* Make a new folder "statistics" in the **modules/oe/ directory** of your shop installation. 
* Download the https://github.com/OXIDprojects/statistics-module/archive/master.zip file and unpack it into the created folder.

## Activate Module

- Activate the module in the administration panel.
- Configure the module for collecting information for the generation of reports.

## Collecting information

At the module settings page, please find the option "Activate Logging for Statistics". If activated, the module now logs information about actions like visited products and categories, orders, on-page searches etc. Out of this information, statistic reports can be generated.

## Generating reports

If the module is active, a new navigation point will appear in admin panel: "Statistics -> Stats & Log-Maint.". Please create and view your statistic reports here.

![reports_list](https://cloud.githubusercontent.com/assets/3593099/12267730/3eab94b6-b952-11e5-86ea-03f5877decbc.png)

Creating a new report:
 * Enter the report name and save it
 * Assign areas which should be added to the report by using "Assign Reports"
 * Choose the time frame for the report to be generated
 * Press the "Generate Report" button

A new page should appear now with the generated report in it:

![one_report](https://cloud.githubusercontent.com/assets/3593099/12267735/4179b3ee-b952-11e5-8ad1-58b104d61390.png)

## Uninstall the module

- Disable the module in administration panel and delete the module folder

## License

Licensing of the software product depends on the shop edition used. The software for OXID eShop Community Edition
is published under the GNU General Public License v3. You may distribute and/or modify this software according to
the licensing terms published by the Free Software Foundation. Legal licensing terms regarding the distribution of
software being subject to GNU GPL can be found under http://www.gnu.org/licenses/gpl.html. The software for OXID eShop
Professional Edition and Enterprise Edition is released under commercial license. OXID eSales AG has the sole rights to
the software. Decompiling the source code, unauthorized copying as well as distribution to third parties is not
permitted. Infringement will be reported to the authorities and prosecuted without exception.
