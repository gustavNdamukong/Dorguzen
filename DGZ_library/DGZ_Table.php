<?php

namespace DGZ_library;


/**
 * This class is an improved version of the DGZ_Paginator which provided very basic navigation. Now DGZ_Table offers much more, like:
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
 * $data = new DGZ_Table($data);
 * $data->getData();
 * 
 * -Note that getData should be passed 2 arguments (the number of items u want displayed per page, n the current page number)
 * -To make this work, you should declare some variables before it establishing the GET URL params representing
 *          i) the number of items to display on a page ($limit)
 *          ii) the current page number ($page)
 *
 * e.g.
 *  $pager = new \DGZ_Table($newsData, $filteredCount);

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
    //This includes 'value', 'link', 'params' (optional array), and 'attributes' (optional array)
    private $_extraFieldParameters = [];
    private $_extraColumns = [];

    //making the records clickable
    private $_clickableRecs = false;
    private $_clickableRecLinkTarget = '';
    private $_clickableRecParams = [];

    //make records sortable
    private $_sortable = false;
    private $_sort = 'ASC';


    /**
     * DGZ_Table constructor. Pass it a second parameter which should be the count of the data to be displayed; remember to filter the real count if you have any applicable filters,
     * otherwise the $count will be the total number of records displayed and reflect the number of page links shown in the pagination links, which may not be accurate.
     *
     * This class has a dependency, and that is the DGZ_library\DGZ_Dates class which is injected into the $_dateClass field
     * @param $data
     * @param int $count
     */
    function __construct($data, $count = 0)
    {
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

        $tableTemplate = $view->show();

        $this->_limit = $limit;
        $this->_page = $page;

        if ( $this->_limit == 'all' ) {
            $data = $this->_data;
        } else {
            $data = array_slice($this->_data, ( ( $this->_page - 1 ) * $this->_limit ), $this->_limit);
        }

        $this->_data = $data;

        $HTMLTable = "<div class='table-responsive'>
                            <table class='table'>
                                <thead>
                                    <tr>";
                                    foreach ($tableTemplate as $result => $heading)
                                    {
                                        if ($this->_sortable) {
                                            if ((isset($_GET['ord'])) && ($_GET['ord'] == $result)) {
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

                        if (!empty($this->_extraColumns)) {
                            foreach ($this->_extraColumns as $head => $valueArray)
                            {
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

        $HTMLTable .= "</tr></thead><tbody><tr>";
        $recCount = count($this->_data );
        $iteration = 0;
        foreach($this->_data as $ref => $dat)
        {
            foreach($dat as $col => $val) {
                if (array_key_exists($col, $tableTemplate)) {
                    if ($this->_clickableRecs) {
                        $linkTarget = $this->_clickableRecLinkTarget;
                        $paramCount = count($this->_clickableRecParams);
                        $check = 1;
                        if (!empty($paramCount)) {
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
                                }
                            }
                            //-------------------------
                            $HTMLTable .= "<td id='".$recId."_".$col."'><a href='$linkTarget'>" . $val . "</a></td>";
                        }
                        else
                        {
                            foreach ($dat as $key => $re) {
                                if (preg_match('/_id/', $key)) {
                                    $recId = $re;
                                    $linkTarget .= '&' . $key . '=' . $re;
                                }
                            }

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
                        foreach ($dat as $key => $re) {
                            if (preg_match('/_id/', $key)) {
                                $recId = $re;
                            }
                        }

                        if (preg_match('/date/', $col))
                        {
                            $HTMLTable .= "<td id='".$recId."_".$col."'>" . $this->_dateClass->YYYYMMDDtoDDMMYYYY($val) . "</td>";
                        }
                        else {
                        $HTMLTable .= "<td id='".$recId."_".$col."'>" . $val . "</td>";}
                    }
                }
            }

            if (!empty($this->_extraColumns)) {
                foreach ($this->_extraColumns as $head => $valuesArray) {
                    foreach ($valuesArray as $type => $vals) {
                        if ($type == 'text') {
                            $HTMLTable .= "<td>" . $vals['value'] . "</td>";
                        }
                        if ($type == 'button') {
                            foreach ($vals as $buttonType => $attributes)
                            {
                                $link = $attributes['link'];
                                if (!empty($attributes['params']))
                                {
                                    $link .= '&';
                                    $count = count($attributes['params']);
                                    $x = 1;
                                    foreach ($attributes['params'] as $param)
                                    {
                                        if ($x < $count) {
                                            $link .= $param . '='.$dat[$param].'&';
                                        }
                                        else
                                        {
                                            $link .= $param . '='.$dat[$param];
                                        }
                                        $x++;
                                    }
                                }

                                $linkAttributes = '';
                                if (!empty($attributes['attributes']))
                                {
                                    $attributeCount = count($attributes['attributes']);
                                    $i = 1;
                                    foreach ($attributes['attributes'] as $attrib => $attribVal)
                                    {
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

        $HTMLTable .= "</tbody>";
        $HTMLTable .= "</table></div>";

        return $HTMLTable;


    } 




    

    public function createLinks( $links, $linkTarget, $list_class) {
        if ( $this->_limit == 'all' ) {
            return '';
        }

        if ($this->_total == 0)
        {
            return '';
        }

        if ($this->_total <= $this->_limit)
        {
            return '';
        }

        $last       = ceil( $this->_total / $this->_limit );

        $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

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




