<?php

namespace DGZ_library;


/**
 * This class is an improved version of the DGZ_Paginator which provided very basic navigation. Now DGZ_Pager offers much more, like:
 *      i) pagination through pages, same as DGZ_Paginator
 *      ii) ability to display that data in a responsive table with paginated data display
 *      iii) ability to sort the columns of that table
 *      iv) ability to click a record in that table to go to the details of it
 *      v) ability to put edit/delete buttons alongside the table row to edit or delete the row record
 *
 * It takes an array as first param and an optional number of items to be displayed per page as 2nd param
 * You can then call its getData() method to retrieve the chunk of data you want for every page
 *
 * Call this class like so
 * $data = new DGZ_Pager($data);
 * $data->getData();
 *
 * -Note that getData should be passed 2 arguments (the number of items u want displayed per page, n the current page number)
 * -To make this work, you should declare some variables before it establishing the GET URL params representing
 *          i) the number of items to display on a page ($limit)
 *          ii) the current page number ($page)
 *
 * e.g.
 *  $pager = new \DGZ_Pager($newsData, $filteredCount);

//set pagination vars
$limit = ( isset( $_GET['limit'] ) ) ? $_GET['limit'] : 3;
$page  = ( isset( $_GET['pageNum'] ) ) ? $_GET['pageNum'] : 1;

$newsData = $pager->getData($limit, $page); //This will work because every time the view file is refreshed, $limit and $page  would be updated
 *
 *
 * @param array $data. This class takes an array or a collection of objects. If it is a collection,
 * 		it will convert it into an array so that it can process
 * @param optional $count. Pass this class a count number in case the data you want to display will not have a count that is equal
 * 		to the original count of the data you pass to it based on filtering you will do to the result
 *
 * @Author Gustav
 */
class DGZ_Table
{

    private $_limit; //This shows the number of records per page
    private $_page; // default start page number
    private $_data;
    private $_total;
    private $_dateClass;

    //adding extra columns n fields on the fly
    private $_extraFieldParameters = []; //This includes 'value', 'link', 'params' (optional array), and 'attributes' (optional array)
    private $_extraColumns = [];

    //making the records clickable
    private $_clickableRecs = false;
    private $_clickableRecLinkTarget = '';
    private $_clickableRecParams = [];

    //make records sortable
    private $_sortable = false;
    private $_sort = 'ASC';


    /**
     * DGZ_Pager constructor. Pass it a second parameter which should be the count of the data to be displayed; remember to filter the real count if you have any applicable filters,
     * otherwise the $count will be the total number of records displayed and reflect the number of page links shown in the pagination links, which may not be accurate.
     *
     * This class has a dependency, and that is the DGZ_library\DGZ_Dates class which is injected into the $_dateClass field
     * @param $data
     * @param int $count
     */
    function __construct($data, $count = 0)
    {
        //our paginator class needs an array, so if our data is an object, we need to convert it into an array
        if (is_object($data))
        {
            $data = $this->entity2array($data);
        }

        $this->_data = $data;
        //get the count of the data to be displayed
        if ($count != 0)
        {
            $count = $count;
        }
        else
        {
            $count = count($data);
        }

        $this->_total = $count;
        $this->_dateClass = new DGZ_Dates();
    }













    /**
     * This is a method for dynamically adding extra columns on the fly to the paginated table to be created by this Pager class. Pass it the text for its heading.
     *
     *You need to call this method before finally calling the getTable() method, as this method will have prepared the array that getTable() will use to build final table output.
     * The two parameters you pass to this method are assigned to the $_extraColumns array just as they are, with the first parameter as the key, and the second parameter as its sub array
     *
     * @param $heading
     * @param array $value contains a multidimensional array with a key of 'text', or 'button'
     *  if it is 'text', then this array will hold just on sub array where the value is the value of the text
     *  if is is a button, this array will have 3 sub arrays i) the value (text) of the button ii) the link target, and iii) an array of parameters to pass at the end of the link
     *
     *@return void;
     */
    public function addColumn($heading)
    {
        //The $this->_extraColumns member could take multiple headings depending on how many times addColumn() is called to create multiple columns
        // so each $heading will have a different value and be a separate multidimensional array within the one $this->_extraColumns array
        $this->_extraColumns[$heading] = [];
    }








    /**
     * Call this method if the extra column field you are adding will contain a button.
     *  This method builds up the _extraFieldParameters array property so that we properly deals with cases where buttons, or text column fields are being created
     *  For buttons, it builds up 3 sub arrays
     *      i) the value (text) of the button
     *      ii) the link target, and
     *      iii) an array of parameters to pass at the end of the link
     *
     * @param $heading string this should match the text you gave to the heading when you first created the new column using addColumn()
     *          the system will then know which heading to place this under
     * @param $buttonType string tell the system what type of button you want to create. We currently handle two button types; 'Edit', and 'Delete' buttons
     * @param $value string the text to go on the button
     * @param $link string the link where these buttons will take the user to e.g. 'index.phtml?page=blogController&action=editPost'
     * @param array $params array of strings of parameters to stick after the link as a query string. Note that these should match the names of DB table fields where the data is coming from e.g. ['blog_id']
     *
     * @return void
     */
    public function addFieldButton($heading, $buttonType, $value, $link, $params = [], $attributes = [])
    {
        $this->_extraFieldParameters['button'][$buttonType]['value'] = $value;
        $this->_extraFieldParameters['button'][$buttonType]['link'] = $link;
        $this->_extraFieldParameters['button'][$buttonType]['params'] = $params;
        $this->_extraFieldParameters['button'][$buttonType]['attributes'] = $attributes;

        $this->_extraColumns[$heading]= $this->_extraFieldParameters;
    }









    /**
     * This is the equivalent of the addFieldButton method because it also builds up the _extraFieldParameters array property so that we properly deal with cases where buttons,
     * or text column fields are being created. It handles placing of text in the extra column field, and not a button, therefore it is thus much simpler.
     *
     * Unlike the case of buttons where we build 3 sub arrays, this array will hold only one sub array where the value is the value of the text
     *
     * @param $heading string this should match the text you gave to the heading when you first created the new column using addColumn()
     *          the system will then know which heading to place this under
     * @param $value string to go in the field
     *
     * @return void
     */
    public function addFieldText($heading, $value)
    {
        $this->_extraFieldParameters['text']['value'] = $value;
        $this->_extraColumns[$heading]= $this->_extraFieldParameters;
    }


    /**
     * @param bool $clickable
     * @param $clickableRecLinkTarget, the link to send the request to which in DGZ is always the controllerName/method name e.g.
     *      $this->controller->settings->getFileRootPath().'feedback/testimonial?'
     * @param array $params contains strings which MUST match the name of your table column who's value you are sending as a URL query string
     *      Also, the method handling this request on your controller MUST take a parameter whose name matches the field name e.g.
     *      $client_name for 'client_name', $id for 'id' etc.
     */
    public function makeClickable($clickable = true, $clickableRecLinkTarget, $params = [])
    {
        $this->_clickableRecs = $clickable;
        $this->_clickableRecLinkTarget = $clickableRecLinkTarget;
        $this->_clickableRecParams = $params;
    }








    public function makeSortable($boolean)
    {
        $this->_sortable = $boolean;
    }









    /**
     * Each time you call this method, pass it the number of items you want displayed on the page, and the current page number
     *
     * @param limit-which is the number of records you want displayed on a page. It could be one of two values; a number for number of pages, or 'all'
     *      for all records to be displayed in one page and no pagination
     * @param page-which is the current page number
     * @return data
     */
    public function getData( $limit = 20, $page = 1 ) {

        $this->_limit   = $limit;
        $this->_page    = $page;

        if ( $this->_limit == 'all' ) {
            $data = $this->_data;
        } else {
            $data = array_slice($this->_data, ( ( $this->_page - 1 ) * $this->_limit ), $this->_limit);
        }

        $this->_data   = $data;

        return $this->_data;
    }









    /**
     * Call this pager class's constructor first passing it your data before calling this method to get the table output
     *
     * @param string $tableTemplateClassName
     * @param $sortLinkTarget string
     * @param int $limit
     * @param $page int
     * @return array
     *
     */
    public function getTable($tableTemplateClassName, $sortLinkTarget = '', $limit = 20, $page = 1 ) {


        //grab table views placed either inside the views folder or the views\admin sub-folder
        $fileNameBase = 'views/' . $tableTemplateClassName . '.php';
        $adminFileNameBase = 'views/admin/' . $tableTemplateClassName . '.php';
        if(file_exists($fileNameBase)) {
            include_once($fileNameBase);
            $viewClass = 'views\\'. $tableTemplateClassName;
        }
        elseif(file_exists($adminFileNameBase)) {
            include_once($adminFileNameBase);
            $viewClass = 'views\admin\\'. $tableTemplateClassName;
        }

        $view = new $viewClass;

        //get the table view template
        $tableTemplate = $view->show();

        //get the data n prepare to map it to a table
        $this->_limit = $limit;
        $this->_page = $page;

        if ( $this->_limit == 'all' ) {
            $data = $this->_data;
        } else {
            $data = array_slice($this->_data, ( ( $this->_page - 1 ) * $this->_limit ), $this->_limit);
        }

        $this->_data = $data;

        //now build the HTML table
        $HTMLTable = "<div class='table-responsive'>
                            <table class='table'>
                                <thead>
                                    <tr>";
        foreach ($tableTemplate as $result => $heading)
        {
            //have they specified that they want the table to be sortable?
            if ($this->_sortable) {
                if ((isset($_GET['ord'])) && ($_GET['ord'] == $result)) {
                    //it means they were already ordering by this column but now want to switch the ordering
                    if ($_GET['s'] == 'ASC') {
                        $sort = 'DESC';
                    }
                    else {
                        $sort = $this->_sort;
                    }
                    $HTMLTable .= "<th class='text-center'><a style='color: white;' href='$sortLinkTarget&ord=$result&s=$sort'>" . $heading . " <i class='fa fa-fw fa-sort'></i></a></th>";
                }
                else {
                    $HTMLTable .= "<th class='text-center'><a style='color: white;' href='$sortLinkTarget&ord=$result&s=$this->_sort'>" . $heading . " <i class='fa fa-fw fa-sort'></i></a></th>";
                }
            }
            else
            {
                $HTMLTable .= "<th class='text-center'>" . $heading . "</th>";
            }

        }
        //check if extra columns were specified and add their headings here before you proceed
        if (!empty($this->_extraColumns)) {
            foreach ($this->_extraColumns as $head => $valueArray)
            {
                //we need to know if the values of this field will be buttons, n if so how many btns are there, so we can make the header wide enough to contain the columns
                //we are of course assuming below that there will not be more than two btns provided for one column, if u decide to accept more in your app, simply come here
                //n add more conditionals like: if ($btnCount == 2) { etc

                //we know there're only two types of buttons handled; 'text' n 'button', so let's get the count of total columns added on the fly
                $textExtraColumnsCount = 0;
                $btnExtraColumnsCount = 0;

                foreach ($valueArray as $type => $vals) {
                    if ($type == 'text') {
                        $textExtraColumnsCount = count($vals);
                    }
                    if ($type == 'button') {
                        $btnExtraColumnsCount = count($vals);
                    }
                }
                $extraColumnsCount = $textExtraColumnsCount + $btnExtraColumnsCount;


                //if ($btnCount == 2) {
                if ($extraColumnsCount > 1) {
                    $HTMLTable .= "<th class='text-center' colspan='$extraColumnsCount'>" . $head . "</th>";
                }
                else
                {
                    $HTMLTable .= "<th class='text-center'>" . $head . "</th>";
                }




            }
        }

        //close the table heading
        $HTMLTable .= "</tr></thead><tbody><tr>";
        $recCount = count($this->_data );
        $iteration = 0;
        foreach($this->_data as $ref => $dat)
        {
            foreach($dat as $col => $val) {
                //only display in the table what (fields) the user has specified in their table template view class-where $col is the DB field names of the data
                if (array_key_exists($col, $tableTemplate)) {
                    //If they wanted the records clickable, then cater for that
                    if ($this->_clickableRecs) {

                        //did they provide a link target
                        $linkTarget = $this->_clickableRecLinkTarget;
                        $paramCount = count($this->_clickableRecParams);
                        $check = 1;
                        if (!empty($paramCount)) {
                            //They provided parameters to pass to the server with the link, so add them to the link
                            foreach ($dat as $key => $re) {
                                if (in_array($key, $this->_clickableRecParams)) {
                                    if ($check < $paramCount) {
                                        $linkTarget .= '&' . $key . '=' . $re . '&';
                                    }
                                    else
                                    {
                                        $linkTarget .= '&' . $key . '=' . $re;
                                    }
                                }
                            }

                            //-----------------------------
                            foreach ($dat as $key => $re) {
                                if (preg_match('/_id/', $key)) {
                                    $recId = $re;
                                    //$key is the column name of the DB ID field and $re is its value (actual record ID)
                                }
                            }
                            //-------------------------
                            $HTMLTable .= "<td id='".$recId."_".$col."'><a href='$linkTarget'>" . $val . "</a></td>";
                        }
                        else
                        {
                            //if they did not provide any parameter, send the DB ID of the rec by default if available, btw it should be always available if they used the DGZ naming convention
                            //to build their tables, otherwise which they should not be trying to use this page class anyway
                            foreach ($dat as $key => $re) {
                                if (preg_match('/_id/', $key)) {
                                    $recId = $re;
                                    //$key is the column name of the DB ID field and $re is its value (actual record ID)
                                    $linkTarget .= '&' . $key . '=' . $re;
                                }
                            }

                            //We need to give every <td> tag an ID made of 'the unique record ID_the DB field name' e.g. '6_newsletter_name'
                            //Then we will also place that ID as a data attribute (custom attribute) of any button u choose to have on the table (both Edit and Delete).
                            //This is an extra service to expand the capabilities of this table engine, and is a bonus for u the developer in case u need to use JS to control the <td> values of the
                            //table on the click of any of those buttons
                            //That said: $key is the column name of the DB ID field, while $re is its value (the actual ID of the record. $col is the name of the  DB column
                            //////$HTMLTable .= "<td><a href='$linkTarget'>" . $val . "</a></td>";
                            /////$HTMLTable .= "<td id='".$recId."_".$col."'><a href='$linkTarget'>" . $val . "</a></td>";///////////////////////////
                            //If its a date field, convert the date from DB to a regular (human-readable) format
                            if (preg_match('/date/', $col))
                            {
                                $HTMLTable .= "<td id='".$recId."_".$col."'><a href='$linkTarget'>" . $this->_dateClass->YYYYMMDDtoDDMMYYYY($val) . "</a></td>";
                            }
                            else {
                                $HTMLTable .= "<td id='".$recId."_".$col."'><a href='$linkTarget'>" . $val . "</a></td>";
                            }
                        }
                    }
                    else
                    {
                        //The user did not request the records to be clickable, so we do not inject any link string into the <td> tag
                        //-----------------------------
                        foreach ($dat as $key => $re) {
                            if (preg_match('/_id/', $key)) {
                                $recId = $re;
                                //$key is the column name of the DB ID field and $re is its value (actual record ID)
                            }
                        }
                        //-------------------------

                        //If its a date field, convert the date from DB to a regular (human-readable) format
                        if (preg_match('/date/', $col))
                        {
                            $HTMLTable .= "<td id='".$recId."_".$col."'>" . $this->_dateClass->YYYYMMDDtoDDMMYYYY($val) . "</td>";
                        }
                        else {
                            $HTMLTable .= "<td id='".$recId."_".$col."'>" . $val . "</td>";}
                    }
                }
            }


            //now for every iteration, loop though the extra columns and insert their values
            ###########
            if (!empty($this->_extraColumns)) {
                foreach ($this->_extraColumns as $head => $valuesArray) { ////////////////////// $this->_extraFieldParameters['text']['value'] = $value;
                    //$valuesArray is a multidimensional array (which could be one of 'button', 'text'), so loop again
                    foreach ($valuesArray as $type => $vals) {
                        //but because a 'button' type will contain a diff kinda sub-array from a 'text' type
                        // we need to check what type it is before looping again, so we know how to loop over each sub-array
                        if ($type == 'text') {
                            //this is easy, text has only one item in its sub-array, n that's the value of the text
                            $HTMLTable .= "<td>" . $vals['value'] . "</td>";
                        }
                        if ($type == 'button') {
                            //its a button, n buttons could have one, or two multidimensional arrays; 'edit', and, or 'delete' with each one having an array of three items
                            // so loop through the buttons
                            foreach ($vals as $buttonType => $attributes)
                            {
                                //Now we need to build the button using its parameters ('vale', 'link', and 'params') from the sub array provided
                                //but first, let's prepare the link wh is crucial for the button to be useful
                                $link = $attributes['link'];

                                //did they provide any parameters for the button link?
                                if (!empty($attributes['params']))
                                {
                                    $link .= '&';
                                    $count = count($attributes['params']);
                                    $x = 1;
                                    foreach ($attributes['params'] as $param)
                                    {
                                        //add them to the link
                                        if ($x < $count) {
                                            //${$param} below will contain the value of the $col from the DB
                                            $link .= $param . '='.$dat[$param].'&';
                                        }
                                        else
                                        {
                                            $link .= $param . '='.$dat[$param];
                                        }
                                        $x++;
                                    }
                                }

                                //Did they provide any attributes for the button link element? If so use them to create the element. These attributes are different from link query strings as is the case
                                //with link parameters
                                $linkAttributes = '';
                                if (!empty($attributes['attributes']))
                                {
                                    $attributeCount = count($attributes['attributes']);
                                    $i = 1;
                                    foreach ($attributes['attributes'] as $attrib => $attribVal)
                                    {
                                        //build the attribute string that we will inject into the button element e. g. data-toggle='modal' or data-target='#editNewsletterModal' or id='clickMe' etc
                                        if ($i < $attributeCount) {
                                            //create the link
                                            $linkAttributes .= $attrib .'="'.$attribVal.'" ';
                                        }
                                        else
                                        {
                                            $linkAttributes .= $attrib . '="'.$attribVal.'"';
                                        }
                                        $i++;
                                    }
                                }

                                //now create the button - you can optionally check for the $buttonType value and style the button accordingly
                                //Note that the $recId variable used in the jQuery custom attribute (data-recid) below has been set above where we create the main table body <td> tags, as we used
                                //the record IDs prefixed with an underscore to the DB field names as the IDs of those <td> tags. This is to give you a way to use these btn links to uniquely
                                // manipulate the rows of the table
                                $btn = '<a data-recid="'.$recId.'" '.$linkAttributes.' href="'.$link.'" class="btn btn-info btn-sm">'.$attributes['value'].'</a>';
                                $HTMLTable .= "<td>" . $btn . "</td>";
                            }
                        }
                    }
                }
            }

            $iteration++;
            if ($iteration != $recCount) {
                $HTMLTable .= "</tr>";
            }
        }
        //close the table
        $HTMLTable .= "</tbody>";
        $HTMLTable .= "</table></div>";

        return $HTMLTable;


    }




    /**
     * We only hit this method when the records exceed our specified max num of records on a page
     *
     * @param $links
     * @param $linkTarget
     * @param $list_class
     * @return string
     */
    public function createLinks( $links, $linkTarget, $list_class) {
        //If we're going to show all the records on one page, no need to show nav links then
        if ( $this->_limit == 'all' ) {
            return '';
        }

        //If there are no records, there's no need to show nav links then
        if ($this->_total == 0)
        {
            return '';
        }

        //If the total records is not greater than the number of recs to be displayed per page, no need to show links
        if ($this->_total <= $this->_limit)
        {
            return '';
        }

        $last       = ceil( $this->_total / $this->_limit );

        $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;
        //die('Total count is: '.$this->_total .'Start is '.$start.'End is '.$end);///////////////////

        $html       = '<ul class="' . $list_class . '">';

        $class      = ( $this->_page == 1 ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="'.$linkTarget.'&limit=' . $this->_limit . '&pageNum=' . ( $this->_page - 1 ) . '">&laquo;</a></li>';

        if ( $start > 1 ) {
            $html   .= '<li><a href="'.$linkTarget.'&limit=' . $this->_limit . '&pagNume=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }

        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->_page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="'.$linkTarget.'&limit=' . $this->_limit . '&pageNum=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="'.$linkTarget.'&limit=' . $this->_limit . '&pageNum=' . $last . '">' . $last . '</a></li>';
        }

        $class      = ( $this->_page == $last ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="'.$linkTarget.'&limit=' . $this->_limit . '&pageNum=' . ( $this->_page + 1 ) . '">&raquo;</a></li>';

        $html       .= '</ul>';


        return $html;
    }










    function entity2array($entities) {
        $result = array();

        foreach ($entities as $entity) {
            $result[] = $entity;
        }
        return $result;
    }

}

?>




