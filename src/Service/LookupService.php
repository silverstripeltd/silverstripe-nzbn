<?php

namespace Somar\NZBN\Service;

use SilverStripe\Core\Config\Configurable;

class LookupService
{
    use Configurable;

    /**
     * @config
     * @var string
     */
    private static $url = 'https://api.business.govt.nz/gateway/nzbn/v5';

    /**
     * @config
     * @var string
     */
    private static $subscription_key;

    /**
     * @param string $nzbn
     * @return null|array
     */
    public function get($nzbn)
    {
        $data = $this->api('/entities/' . $nzbn);

        if (empty($data) || isset($data['errorMessage'])) {
            return null;
        }

        $entityData = [];
        $entityTypeMap = [
            'LTD' => 'NZ Limited Company',
            'ULTD' => 'NZ Unlimited Company',
            'COOP' => 'NZ Co-operative Company',
            'ASIC' => 'Overseas ASIC Company',
            'NON_ASIC' => 'Overseas Non-ASIC Company',
            'Sole_Trader' => 'Sole Trader',
            'Partnership' => 'Partnership',
            'Trading_Trust' => 'Trust',
            'T' => 'Charitable Trust',
            'B' => 'Building Society',
            'D' => 'Credit Union',
            'F' => 'Friendly Society',
            'I' => 'Incorporated Society',
            'N' => 'Industrial & Provident Society',
            'Y' => 'Limited Partnership (NZ)',
            'Z' => 'Limited Partnership (Overseas)',
            'GovtCentral' => 'Central government agencies',
            'GovtLocal' => 'Local government agencies',
            'GovtEdu' => 'Government educational facilities, including schools',
            'GovtOther' => 'Other government agencies',
        ];

        $entityStatusMap = [
            50 => 'Registered',
            55 => 'Voluntary Administration',
            56 => 'Voluntary Administration & In Receivership',
            57 => 'Voluntary Administration & In Receivership',
            60 => 'In Liquidation',
            61 => 'In Receivership & In Liquidation',
            62 => 'Removed',
            63 => 'Voluntary Administration & In Receivership & In Liquidation',
            64 => 'Voluntary Administration & In Receivership & In Liquidation',
            65 => 'Voluntary Administration & In Liquidation',
            66 => 'Voluntary Administration & In Receivership & In Liquidation',
            70 => 'In Receivership',
            71 => 'In Receivership & In Liquidation',
            72 => 'In Statutory Administration',
            80 => 'Removed',
            90 => 'Inactive (unincorporated entities only)',
            91 => 'Closed/Disestablished (unincorporated entities only)',
        ];

        $gstStatusMap = [
            'REGISTERED' => 'Registered',
            'NON-REGISTERED' => 'Not Registered',
        ];

        $entityData['TypeOfCompany'] = !empty($entityTypeMap[$data['entityTypeCode']])
            ? $entityTypeMap[$data['entityTypeCode']] : null;

        $entityData['CompanyStatus'] = !empty($entityStatusMap[(int)$data['entityStatusCode']])
            ? $entityStatusMap[(int)$data['entityStatusCode']] : null;

        $entityData['LegalName'] = !empty($data['entityName']) ? $data['entityName'] : null;
        $entityData['GSTStatus'] = !empty($data['gstStatus']) ?
            !empty($gstStatusMap[$data['gstStatus']]) ? $gstStatusMap[$data['gstStatus']] : null :
            null;
        $entityData['GSTEffectiveDate'] = !empty($data['gstEffectiveDate'])
            ? date('d/m/Y', strtotime($data['gstEffectiveDate'])) : null;

        $entityData['AustralianBusinessNumber'] = !empty($data['australianBusinessNumber'])
            ? $data['australianBusinessNumber'] : null;

        $entityData['AustralianCompanyNumber'] = !empty($data['australianCompanyNumber'])
            ? $data['australianCompanyNumber'] : null;

        if (!empty($data['tradingNames']) && count($data['tradingNames']) > 1) {
            foreach ($data['tradingNames'] as $tradeName) {
                if (!empty($tradeName['name']) && empty($tradeName['endDate'])) {
                    $entityData['TradingName'] = $tradeName['name'];
                    break;
                }
            }
        } else {
            $entityData['TradingName'] = !empty($data['tradingNames'][0]['name'])
                ? $data['tradingNames'][0]['name'] : null;
        }

        if (empty($entityData['TradingName']) && !empty($entityData['LegalName'])) {
            $entityData['TradingName'] = $entityData['LegalName'];
        }

        if (!empty($data['registeredAddress'])) {
            if (count($data['registeredAddress']) > 1) {
                foreach ($data['registeredAddress'] as $address) {
                    if (empty($address['endDate'])) {
                        $entityData['RegisteredAddress_Line1'] = !empty($address['address1'])
                            ? $address['address1'] : null;

                        $entityData['RegisteredAddress_Line2'] = !empty($address['address2'])
                            ? $address['address2'] : null;

                        $entityData['RegisteredAddress_City'] = !empty($address['address3'])
                            ? $address['address3'] : null;

                        $entityData['RegisteredAddress_PostalCode'] = !empty($address['postCode'])
                            ? $address['postCode'] : null;

                        $entityData['RegisteredAddress_Country'] = !empty($address['countryCode'])
                            ? $address['countryCode'] : null;

                        break;
                    }
                }
            } else {
                $entityData['RegisteredAddress_Line1'] = !empty($data['registeredAddress'][0]['address1'])
                    ? $data['registeredAddress'][0]['address1'] : null;

                $entityData['RegisteredAddress_Line2'] = !empty($data['registeredAddress'][0]['address2'])
                    ? $data['registeredAddress'][0]['address2'] : null;

                $entityData['RegisteredAddress_City'] = !empty($data['registeredAddress'][0]['address3'])
                    ? $data['registeredAddress'][0]['address3'] : null;

                $entityData['RegisteredAddress_PostalCode'] = !empty($data['registeredAddress'][0]['postCode'])
                    ? $data['registeredAddress'][0]['postCode'] : null;

                $entityData['RegisteredAddress_Country'] = !empty($data['registeredAddress'][0]['countryCode'])
                    ? $data['registeredAddress'][0]['countryCode'] : null;
            }
        }

        if (!empty($data['principalPlaceOfActivity'])) {
            if (count($data['principalPlaceOfActivity']) > 1) {
                foreach ($data['principalPlaceOfActivity'] as $address) {
                    if (empty($address['endDate'])) {
                        $entityData['HeadOfficeAddress_Line1'] = !empty($address['address1'])
                            ? $address['address1'] : null;

                        $entityData['HeadOfficeAddress_Line2'] = !empty($address['address2'])
                            ? $address['address2'] : null;

                        $entityData['HeadOfficeAddress_City'] = !empty($address['address3'])
                            ? $address['address3'] : null;

                        $entityData['HeadOfficeAddress_PostalCode'] = !empty($address['postCode'])
                            ? $address['postCode'] : null;

                        $entityData['HeadOfficeAddress_Country'] = !empty($address['countryCode'])
                            ? $address['countryCode'] : null;

                        break;
                    }
                }
            } else {
                $entityData['HeadOfficeAddress_Line1'] = !empty($data['principalPlaceOfActivity'][0]['address1'])
                    ? $data['principalPlaceOfActivity'][0]['address1'] : null;

                $entityData['HeadOfficeAddress_Line2'] = !empty($data['principalPlaceOfActivity'][0]['address2'])
                    ? $data['principalPlaceOfActivity'][0]['address2'] : null;

                $entityData['HeadOfficeAddress_City'] = !empty($data['principalPlaceOfActivity'][0]['address3'])
                    ? $data['principalPlaceOfActivity'][0]['address3'] : null;

                $entityData['HeadOfficeAddress_PostalCode'] = !empty($data['principalPlaceOfActivity'][0]['postCode'])
                    ? $data['principalPlaceOfActivity'][0]['postCode'] : null;

                $entityData['HeadOfficeAddress_Country'] = !empty($data['principalPlaceOfActivity'][0]['countryCode'])
                    ? $data['principalPlaceOfActivity'][0]['countryCode'] : null;
            }

            if (empty($entityData['HeadOfficeAddress_Line1']) &&
                empty($entityData['HeadOfficeAddress_Line2']) &&
                empty($entityData['HeadOfficeAddress_PostalCode']) &&
                empty($entityData['HeadOfficeAddress_Country'])) {

                $entityData['HeadOfficeAddress_Line1'] = isset($entityData['RegisteredAddress_Line1'])
                    ? $entityData['RegisteredAddress_Line1'] : null;

                $entityData['HeadOfficeAddress_Line2'] = isset($entityData['RegisteredAddress_Line2'])
                    ? $entityData['RegisteredAddress_Line2'] : null;

                $entityData['HeadOfficeAddress_City'] = isset($entityData['RegisteredAddress_City'])
                    ? $entityData['RegisteredAddress_City'] : null;

                $entityData['HeadOfficeAddress_PostalCode'] = isset($entityData['RegisteredAddress_PostalCode'])
                    ? $entityData['RegisteredAddress_PostalCode'] : null;

                $entityData['HeadOfficeAddress_Country'] = isset($entityData['RegisteredAddress_Country'])
                    ? $entityData['RegisteredAddress_Country'] : null;
            }
        }

        return $entityData;
    }

    /**
     * @param string $query
     * @return array|null
     */
    public function search($query)
    {
        $data = $this->api('/entities/?search-term=' . $query);

        if (empty($data) || isset($data['errorMessage'])) {
            return null;
        }

        return $data;
    }

    /**
     * @param string $url
     * @return null|array
     */
    private function api($url)
    {
        $apiUrl = self::config()->get('url');
        $subscriptionKey = self::config()->get('subscription_key');

        if (!$subscriptionKey) {
            throw new \RuntimeException('NZBN API subscription key is invalid: ' . $subscriptionKey);
        }

        $curl = curl_init($apiUrl . $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Ocp-Apim-Subscription-Key: ' . $subscriptionKey,
            'Accept: application/json',
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            return null;
        }

        curl_close($curl);

        return json_decode((string) $response, true);
    }
}
