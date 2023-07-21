<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace Beehive\Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericFilter extends \Beehive\Google\Model
{
    /**
     * @var string
     */
    public $operation;
    protected $valueType = GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericValue::class;
    protected $valueDataType = '';
    /**
     * @param string
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }
    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }
    /**
     * @param GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericValue
     */
    public function setValue(GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericValue $value)
    {
        $this->value = $value;
    }
    /**
     * @return GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericValue
     */
    public function getValue()
    {
        return $this->value;
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
\class_alias(GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericFilter::class, 'Beehive\\Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1alphaAudienceDimensionOrMetricFilterNumericFilter');