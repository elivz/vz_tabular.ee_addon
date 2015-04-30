VZ Tabluar
==========

VZ Tabular is an ExpressionEngine plugin that provides a simple method to output tabular data from the templates.

### Example:

    {exp:vz_tabular:csv}
        {exp:channel:entries channel="news" limit="10"}
            {col:Title}{title}{/col:Title}
            {col:Publish Date}{entry_date format="%Y-%m-%d"}{/col:Publish Date}
            {col:Body}{body_text}{/col:Body}
        {/exp:channel:entries}
    {/exp:vz_tabular:csv}


Output Formats
--------------

### CSV

    {exp:vz_tabular:csv}

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

    {exp:vz_tabular:json pretty="yes"}

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
* `pretty` - Indent the JSON code for easier readability. Default: `no`.


### XML

    {exp:vz_tabular:xml}

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

    {exp:vz_tabular:excel filename="spreadsheet.xlsx"}

#### Parameters

* `filename` - *(Required)* Specify the name of the file that is created. The file should have an `.xlsx` extension. Default: none
* `sheet` - Name of the worksheet. Default: `Sheet1`


### Table

    {exp:vz_tabular:table}

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