<?php

namespace DGZ_library;

/**
 * Call this class like so
 * $data = new DGZ_Paginator($array);
 * $data->paginate($array);
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


    protected $_end = 0;


    protected $_firstPage = 'no';


    protected $_lastPage = 'no';


    protected $_numPages = 0;


    protected $_humanOffset = 1;


    protected $_humanEnd = 0;




    /**
     * DGZ_Paginator constructor. Here we set things that are fixed values, that will not change with page refreshes
     *
     * @param $display_array the array of data a user is paging through in a view
     */
    public function __construct($display_array)
    {
        $this->_data = $display_array;

        $this->_totalCount = count($display_array);

        //The number of pages can already be determined from the total items count
        $this->_numPages = ceil($this->_totalCount / $this->_numPerPage);

    }



    /**
     * This is the method that is called with each page navigated to while paging through the records.
     *
     * We pass it $page numbers as navigated to by the user eg 1, 2, etc
     * We pass it the offset as the numberPerPage incremented by 1 to make the start record number in the next page in case of next
     *  or decreased by 1 to make the last record on the previous page in case of prev, as the user navigates the data.
     *  Note that the length (end of the items) displayed per page is then worked out from this offset
     *
     * @page the current page number
     * @offset what record number to start displaying from
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

        $this->_end = ($this->_numPerPage);

        $this->_humanOffset = $this->_offset + 1;

        $this->_humanEnd = ($this->_numPerPage * $page);


        if ($page == 1)
        {
            $this->_firstPage = 'yes';
        }


        if ($page == $this->_numPages)
        {
            $this->_lastPage = 'yes';
        }


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




