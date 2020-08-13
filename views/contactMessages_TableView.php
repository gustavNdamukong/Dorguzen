<?php

namespace views;




class contactMessages_TableView extends \DGZ_library\DGZ_HtmlView
{

    /**
     * Make this function return an array where the keys are your actual DB column names
     * and its values are what you will want to call them as seen in the table when it will be displayed in the browser.
     *
     * @return array
     */
    public function show()
    {
        return [
            'contactformmessage_name' => 'Visitor',
            'contactformmessage_email' => 'Email',
            'contactformmessage_phone' => 'Phone',
            'contactformmessage_message' => 'Message',
            'contactformmessage_date' => 'Date',
        ];

    }



}

?>