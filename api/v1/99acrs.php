<?php

        $url =  "http://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/";
        $request = "<?xml version='1.0'?><query><user_name>andhericommercial</user_name><pswd>Andheri@123</pswd><start_date>2021-07-27 00:01:00</start_date><end_date>2021-07-27 23:59:00</end_date></query>";
        $allParams = array('xml'=>$request);
        $leads = get99AcresLeads($allParams,$url);
        //print_r('leads generated'.array_values($leads));
        $xml = simplexml_load_string($leads, null, LIBXML_NOCDATA);
        //echo $xml;
        echo "NOW:->>";
        foreach ($xml->Resp->QryDtl as $element) {
            foreach($element as $key => $val) {
                echo "{$key}: {$val}";
            }
        }

        //echo $xml;
        //print_r('xml'.$xml);
        //$json = json_encode($xml);
        //print_r('json'.$json); 
        //$array = json_decode($json,TRUE); */       
        //print_r('leads generated'.array_values($leads));
        //print_r(vardump($leads));
        
        //print_r('array'.array_values($array));

function get99AcresLeads($allParams,$url){
    $crl = curl_init($url);
    curl_setopt ($crl, CURLOPT_POST, 1);
    curl_setopt ($crl, CURLOPT_POSTFIELDS, $allParams);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER,1);
    return curl_exec ($crl);
}
//sqaurefeetindia99
//andhericommercial
///Andheri@123


/*$xml = "<Xml ActionStatus='true'>
    <Resp>
        <QryDtl ResType='S2M' QueryId='60ffdd8d4f9ef22d08727090'>
            <CmpctLabl>
                <![CDATA[Rs. 1.8 Lac, Ready to move office space for Lease in Kanakia Atrium 215, Chakala,Mumbai Andheri-Dahisar ]]>
            </CmpctLabl>
            <QryInfo>
                <![CDATA[I am interested in this project.]]>
            </QryInfo>
            <RcvdOn>
                <![CDATA[2021-07-27 15:48:53]]>
            </RcvdOn>
            <ProjId>
                <![CDATA[550]]>
            </ProjId>
            <ProjName>
                <![CDATA[Kanakia Atrium 215]]>
            </ProjName>
            <PhoneVerificationStatus>
                <![CDATA[VERIFIED]]>
            </PhoneVerificationStatus>
            <EmailVerificationStatus>
                <![CDATA[VERIFIED]]>
            </EmailVerificationStatus>
            <IDENTITY>
                <![CDATA[Dealer]]>
            </IDENTITY>
            <PROPERTY_CODE>
                <![CDATA[T57140518]]>
            </PROPERTY_CODE>
            <SubUserName>
                <![CDATA[andhericommercial@rdbrothers.com]]>
            </SubUserName>
            <ProdId Status='Active' Type='LP-P'>
                <![CDATA[T57140518]]>
            </ProdId>
        </QryDtl>
        <CntctDtl>
            <Name>
                <![CDATA[Girish Kumar Dubey]]>
            </Name>
            <Email>
                <![CDATA[industriesbroker@gmail.com]]>
            </Email>
            <Phone>
                <![CDATA[+91-9987686868]]>
            </Phone>
        </CntctDtl>
    </Resp>
</Xml>";


//SELECT `id`, `queryid`, `cmpctlabl`, `qryinfo`, `rcvdon`, `projid`, `projname`, `property_code`, `subusername`, `name`, `email`, `phone`, `created_by`, `created_date`, `modified_by`, `modified_date` FROM `99acres` WHERE 1

    /*$xmlObject = simplexml_load_file('sample.xml');

    $jsonData = json_encode($xmlObject, JSON_PRETTY_PRINT);     

    print_r($jsonData);*/


    /* 
    xml=

    <?xml version='1.0'?><query><user_name>andhericommercial </user_name><pswd>Andheri@123</pswd><start_date>2021-07-27 00:00:00</start_date><end_date>2021-07-27 23:59:59</end_date></query>

    */

?>


