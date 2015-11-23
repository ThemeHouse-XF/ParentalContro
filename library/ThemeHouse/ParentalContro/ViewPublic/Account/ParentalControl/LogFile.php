<?php

class ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_LogFile extends XenForo_ViewPublic_Base
{

    public function renderHtml()
    {
        $this->_params['results'] = XenForo_ViewPublic_Helper_Search::renderSearchResults($this,
            $this->_params['results']);

        $this->_response->setHeader('Content-Disposition', 'attachment; filename=logfile.html', true);
    } /* END renderHtml */
}