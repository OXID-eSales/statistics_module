OE Statistics Module
====================

Module for logging and displaying shop statistics (OXID eShop CE/PE only).

Setup
-----

- Clone module to your eShop `modules/oe/` directory:

  .. code:: bash

     git clone https://github.com/OXID-eSales/oestatistics_module.git statistics
- Activate the module in administration area.
- Configure module for collecting the information for reports generation.

Collecting information
----------------------

At the module settings page "Activate Logging for Statistics" checkbox can be found. Whenever it is checked,
module will collect information about shop actions like viewed product and category, purchases, searches and so on.
Later on from this information statistics report can be generated.

Generating reports
------------------

When module is active new navigation options should appear - "Statistics -> Stats & Log-Maint.".
At this page all created reports can be seen and new ones can be created.

.. image:: https://cloud.githubusercontent.com/assets/3593099/12267730/3eab94b6-b952-11e5-86ea-03f5877decbc.png

To create new report:
 * Enter report name and save it
 * Then assign fields, which should be added to report with "Assign Reports"
 * Choose time frame for which report should be generated
 * Press "Generate Report" button

You should now have a new window opened with generated report in it:

.. image:: https://cloud.githubusercontent.com/assets/3593099/12267735/4179b3ee-b952-11e5-8ad1-58b104d61390.png

License
-------

Licensing of the software product depends on the shop edition used. The software for OXID eShop Community Edition
is published under the GNU General Public License v3. You may distribute and/or modify this software according to
the licensing terms published by the Free Software Foundation. Legal licensing terms regarding the distribution of
software being subject to GNU GPL can be found under http://www.gnu.org/licenses/gpl.html. The software for OXID eShop
Professional Edition and Enterprise Edition is released under commercial license. OXID eSales AG has the sole rights to
the software. Decompiling the source code, unauthorized copying as well as distribution to third parties is not
permitted. Infringement will be reported to the authorities and prosecuted without exception.
