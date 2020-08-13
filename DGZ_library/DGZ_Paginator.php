<?php

/*
 * Call this class like so
 * $data = new DGZ_Paginator($array);
 * $data->pagination($array);
 *
 * It takes an array as first param and a page number as 2nd param
 */
class DGZ_Paginator
{

    protected $_data = '';


    protected $_currentPage = 1;


    protected $_totalCount = 0;


    protected $_numPerPage = 10;


    protected $_offset = 0;


    protected $_end = 0; //this is now showPerPage


    protected $_firstPage = 'no';


    protected $_lastPage = 'no';


    protected $_numPages = 0;


    protected $_humanOffset = 1;


    protected $_humanEnd = 0;



    public function __construct($display_array)
    {
        //Here we set things that are fixed values, that will not change with page refreshes
        $this->_data = $display_array;

        $this->_totalCount = count($display_array);

            //The number of pages can already be determined from the total items count
        $this->_numPages = ceil($this->_totalCount / $this->_numPerPage);

    }



    /*
     * We pass it $page numbers as navigated to by the user
     * We pass it the offset as the numberPerPage incremented by 1 in case of next
     *  or decreased by 1 (in case of prev) as the user navigates the data
     *  Note that the length (end of the items) displayed is then worked out from this offset
     *
     */
    public function paginate($page, $offset)
    {
        /* data--
         * current page--1
         * recperpage--10
         * recStart--0
         * recEnd--0
         * totalRecs--0
         * numpages--0
         * firstPage--false
         * lastPage--false
         */

        $this->_currentPage = $page;



        $this->_offset = $offset;
        
        //Note that arrays start from 0 so array_slice() will grab from 0 which means the numPerPage figure must be decreased by 1
        //to get the accurate number per page. These numbers will not look right to humans, so we create a separate pair of offset n end
        // wh we call humanOffset and humanEnd respectively just for displaying on screen.
        /////////////////$this->_end = ($this->_offset + $this->_numPerPage); //the length param of array_slice() does not count the last ending number
        $this->_end = ($this->_numPerPage); //the length param of array_slice() does not count the last ending number


        //set the human-friendly start and end
        $this->_humanOffset = $this->_offset + 1;

        $this->_humanEnd = ($this->_numPerPage * $page); //the length param of array_slice() does not count the last ending number



        //determine when its the first or last page
        if ($page == 1)
        {
            $this->_firstPage = 'yes';
        }



        if ($page == $this->_numPages)
        {
            $this->_lastPage = 'yes';
        }


        //If the array is shorter than the length, then only the available array elements will be present
        $result = array_slice($this->_data, $this->_offset, $this->_end, true);



        return $result;


    }



    public function getCurrentPage()
    {
        return $this->_currentPage;
    }



    public function getTotalCount()
    {
        return $this->_totalCount;
    }



    public function getNumPerPage()
    {
        return $this->_numPerPage;
    }



    public function setNumPerPage($val)
    {
        return $this->_numPerPage = $val;
    }



    public function getOffset()
    {
        return $this->_offset;
    }




    public function getEnd()
    {
        return $this->_end;
    }




    public function getFirstPage()
    {
        return $this->_firstPage;
    }




    public function getLastPage()
    {
        return $this->_lastPage;
    }



    public function getNumPages()
    {
        return $this->_numPages;
    }




    public function getHumanOffset()
    {
        return $this->_humanOffset;
    }



    public function gethumanEnd()
    {
        return $this->_humanEnd;
    }


}

?>




