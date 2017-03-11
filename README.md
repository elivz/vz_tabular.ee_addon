VZ Tabluar
==========

VZ Tabular is an ExpressionEngine plugin that provides a simple method to output tabular data from the templates. Five ouput formats are currently supported: [CSV](#csv), [JSON](#json), [XML](#xml), [HTML Table](#html-table), & [Excel](#excel).

Some possible use-cases include: creating a simple read-only API, endpoints for AJAX operations, and exporting data for transfer to another system. Get creative!

Usage
-----

The tag pair should be wrapped around a looping tag, such as `channel:entries` or a Grid field. Within that loop, each column for output is wrapped in `[col Heading] ... [/col]`, with "Heading" being the title for that column. Depending on the output format, you can usually include spaces in the column heading (XML does not support spaces).

If you have problems getting the output you want, turn on Template Debugging. VZ Tabular outputs detailed information about its process, and any problems that occur.

### Example:

    {exp:vz_tabular:csv}
        {exp:channel:entries channel="news" limit="10"}
            [col Title]{title}[/col]
            [col Publish Date]{entry_date format="%Y-%m-%d"}[/col]
            [col Body]{body_text}[/col]
        {/exp:channel:entries}
    {/exp:vz_tabular:csv}


Output Formats
--------------

### CSV

    {exp:vz_tabular:csv} ... {/exp:vz_tabular:csv}

#### Sample Output

    Title,"Publish Date",Body
    "Article One",2015-03-17,"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet libero tincidunt, convallis est feugiat, pretium nibh.</p>"
    "Article Two",2015-02-03,"<p>Fusce pretium neque magna, ac feugiat felis volutpat ut. Suspendisse id ullamcorper risus, a sodales nisi.</p>"

#### Parameters

* `filename` - Downloads the output to the user's computer, rather than displaying it in the browser. Default: none
* `stop_processing` - If this is set to `yes`, no further template processing will occur after the plugin runs. This is useful if you are creating a data-feed and want to ensure that no template debugging info is included on the page. Default: `no`
* `delimiter` - Default: `,`.
* `enclosure` - Default: `"`.


### JSON

    {exp:vz_tabular:json pretty="yes"} ... {/exp:vz_tabular:json}

#### Sample Output

    [
        {
            "Title": "Article One",
            "Publish Date": "2015-03-17",
            "Body": "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet libero tincidunt, convallis est feugiat, pretium nibh.<\/p>"
        },
        {
            "Title": "Article Two",
            "Publish Date": "2015-02-03",
            "Body": "<p>Fusce pretium neque magna, ac feugiat felis volutpat ut. Suspendisse id ullamcorper risus, a sodales nisi.<\/p>"
        }
    ]

#### Parameters

* `filename` - Downloads the output to the user's computer, rather than displaying it in the browser. Default: none
* `stop_processing` - If this is set to `yes`, no further template processing will occur after the plugin runs. This is useful if you are creating a data-feed and want to ensure that no template debugging info is included on the page. Default: `no`
* `pretty` - Indent the JSON code for easier readability. *Requires PHP 5.4+*. Default: `no`.


### XML

    {exp:vz_tabular:xml} ... {/exp:vz_tabular:xml}

#### Sample Output

    <?xml version="1.0" encoding="UTF-8"?>
    <root>
      <element>
        <Title><![CDATA[Article One]]></Title>
        <Publish_Date><![CDATA[2015-03-17]]></Publish_Date>
        <Body><![CDATA[<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet libero tincidunt, convallis est feugiat, pretium nibh.</p>]]></Body>
      </element>
      <element>
        <Title><![CDATA[Article Two]]></Title>
        <Publish_Date><![CDATA[2015-02-03]]></Publish_Date>
        <Body><![CDATA[<p>Fusce pretium neque magna, ac feugiat felis volutpat ut. Suspendisse id ullamcorper risus, a sodales nisi.</p>]]></Body>
      </element>
    </root>

*Note:* XML does not support spaces in tags, so they are automatically replaced with an underscore.

#### Parameters

* `filename` - Downloads the output to the user's computer, rather than displaying it in the browser. Default: none
* `stop_processing` - If this is set to `yes`, no further template processing will occur after the plugin runs. This is useful if you are creating a data-feed and want to ensure that no template debugging info is included on the page. Default: `no`
* `esc_html` - If this is set to `yes` any HTML code in the data will be escaped (i.e. `<p>Test</p>` would become `&lt;p&gt;Test&lt;/p&gt;`). Otherwise the HTML tags will be left alone and will be wrapped in a CDATA block. Default: `no`
* `root` - Name of the root element that contains the array of items. Default: `root`
* `element` - Name of the elements representing each row. Default: `element`
* `pretty` - Indent the XML code for easier readability. Default: `no`


### Excel

    {exp:vz_tabular:excel filename="spreadsheet.xlsx"} ... {/exp:vz_tabular:excel}

#### Parameters

* `filename` - *(Required)* Specify the name of the file that is created. The file should have an `.xlsx` extension. Default: none
* `sheet` - Name of the worksheet. Default: `Sheet1`


### HTML Table

    {exp:vz_tabular:table} ... {/exp:vz_tabular:table}

#### Sample Output

    <table>
        <thead><tr>
            <th>Title</th>
            <th>Publish Date</th>
            <th>Body</th>
        </tr></thead>
        <tbody
            <tr>
                <td>Article One</td>
                <td>2015-03-17</td>
                <td><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut laoreet libero tincidunt, convallis est feugiat, pretium nibh.</p></td>
            </tr>
            <tr>
                <td>Article Two</td>
                <td>2015-02-03</td>
                <td><p>Fusce pretium neque magna, ac feugiat felis volutpat ut. Suspendisse id ullamcorper risus, a sodales nisi.</p></td>
            </tr>
        </tbody>
    </table>

#### Parameters

* `esc_html` - If this is set to `yes`, any HTML tags in the content will be escaped so the tags display on-screen. Otherwise they will be left alone to render normally. Default: `no`
* `tab` - String to use for indenting the HTML. Set to nothing (`tab=""`) for no indentation. Default: four spaces
* `headers` - Include a header row with the column names. Default: `yes`
* `id` - Sets an ID on the table element. Default: none
* `class` - Sets a class on the table element. Default: none

Alternate Column Syntax
-----------------------

Previous versions of VZ Tabular used curly brackets for the `col` tags. That is still supported but the new, square bracket form is prefered. When curly brackets are used ExpressionEngine tries to parse the template before the plugin is run, causing much higher memory usage and out-of-memory errors in some cases.

### Example of alternate syntax:

    {exp:vz_tabular:csv}
        {exp:channel:entries channel="news" limit="10"}
            {col Title}{title}{/col}
            {col Publish Date}{entry_date format="%Y-%m-%d"}{/col}
            {col Body}{body_text}{/col}
        {/exp:channel:entries}
    {/exp:vz_tabular:csv}

Support
-------

Please post any questions or problems in the [Devot:ee support forum](https://devot-ee.com/add-ons/support/vz-tabular/viewforum).

VZ Tabular uses a modular adapter system for output formats, so if there is another format that would be useful to you, please tell me! 

Changelog
---------

* __1.0.0__: Initial public release.
* __1.0.1__: Fix bug in Excel output format.
* __1.0.2__: Allow global variables to be used in CSV output.
* __1.0.3__: Enable alternate column syntax using square brackets for better performance and lower memory usage. Deprecate previous curly-bracket syntax.
* __1.1.0__: Supports ExpressionEngine 3. Rearranged file structure in download.