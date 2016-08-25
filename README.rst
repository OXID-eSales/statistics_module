OE Statistics Module
====================

A module for logging and displaying statistics for OXID eShop Community and Professional Edition (formerly part of the core)

Requirements
------------

* OXID eShop v6.*

Installation
------------

- Make a new folder "statistics" in the **modules/oe/** directory of your shop installation. Download https://github.com/OXID-eSales/statistics_module/archive/master.zip and unpack it into this folder. **OR**
- Git clone the module to your OXID eShop **modules/oe/** directory:

  .. code:: bash

     git clone https://github.com/OXID-eSales/oestatistics_module.git statistics
- Activate the module in administration panel
- Configure the module for collecting information for the generation of reports

Collecting information
----------------------

At the module settings page, please find the option "Activate Logging for Statistics". If activated, the module now logs information about actions like visited products and categories, orders, on-page searches etc. Out of this information, statistic reports can be generated.

Generating reports
------------------

If the module is active, a new navigation point will appear in admin panel: "Statistics -> Stats & Log-Maint.". Please create and view your statistic reports here.

.. image:: https://cloud.githubusercontent.com/assets/3593099/12267730/3eab94b6-b952-11e5-86ea-03f5877decbc.png

Creating a new report:
 * Enter the report name and save it
 * Assign areas which should be added to the report by using "Assign Reports"
 * Choose the time frame for the report to be generated
 * Press the "Generate Report" button

A new page should appear now with the generated report in it:

.. image:: https://cloud.githubusercontent.com/assets/3593099/12267735/4179b3ee-b952-11e5-8ad1-58b104d61390.png

Uninstall the module
--------------------

- Disable the module in administration panel and/or delete the module folder

License
-------

Licensing of the software product depends on the shop edition used. The software for OXID eShop Community Edition
is published under the GNU General Public License v3. You may distribute and/or modify this software according to
the licensing terms published by the Free Software Foundation. Legal licensing terms regarding the distribution of
software being subject to GNU GPL can be found under http://www.gnu.org/licenses/gpl.html. The software for OXID eShop
Professional Edition and Enterprise Edition is released under commercial license. OXID eSales AG has the sole rights to
the software. Decompiling the source code, unauthorized copying as well as distribution to third parties is not
permitted. Infringement will be reported to the authorities and prosecuted without exception.
