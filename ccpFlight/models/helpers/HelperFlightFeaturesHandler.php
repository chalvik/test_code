<?php


namespace common\modules\ccpFlight\models\helpers;

use common\modules\ccpEmployee\models\Employee;
use common\modules\ccpFlight\models\Flight;
use common\modules\ccpFlight\models\FlightLegCrew;
use common\modules\ccpFlight\models\search\FlightSearch;
use common\modules\EdbPassenger\models\EdbPassenger;
use common\modules\EdbPassenger\models\EdbPassengerCode;
use common\modules\EdbPassenger\models\EdbPassengerTransfer;
use common\modules\EdbPassenger\models\helpers\EdbPassengerExtractor;
use common\modules\EdbPassenger\models\helpers\FlightPassengerAllCodes;
use common\modules\scheduler\models\ExtendedLogger;

class HelperFlightFeaturesHandler
{

    /**
     * @var array
     */
    private static $allCodes = null;
    private static $existedCodes = null;
    private $flight;
    private $featuresFlight;
    private $codes = [];
    private $existed_codes = [];
    private $row_hashes = [];

    /**
     * HelperFlightFeaturesHandler constructor.
     * @param $flight
     */

    public function __construct($flight)
    {
        $this->flight = $flight;
        $this->featuresFlight = HelperFlightFeatures::findOne(['flight_id' => $this->flight->id]) ?: (new HelperFlightFeatures(['flight_id' => $this->flight->id]));
    }

    public function handle()
    {
        // get all codes
        $this->collectCodes();

        // handle and store
        $this->handleNotExistedCodes();

        $this->handleExistedCodes();
        $this->handleFailTransfer();

        $this->collectPosCodes();

        $this->featuresFlight->save();
    }


    /**
     * get all codes
     * @return array
     */
    private function collectCodes()
    {

        if ($passengers = $this->flight->passengers) {
            // preload infants
            EdbPassengerExtractor::infantCodePreload($passengers);
            /** @var EdbPassenger $passenger */
            foreach ($passengers as $passenger) {
                $passenger->extractor->collectCodes();
                $this->codes = array_merge($this->codes, $passenger->codesRaw);
                $this->row_hashes[] = $passenger->ROWHASH;

            }
            // get code unique
            $this->codes = array_unique($this->codes);

        }


        return $this->codes;
    }


    private function handleNotExistedCodes()
    {

        // store different
        $this->storeDifferent();

        $this->featuresFlight->codes = array_diff($this->codes, self::getMapExistedCodes());

    }


    /**
     * @return array
     */
    private
    function handleExistedCodes()
    {
        return $this->featuresFlight->existed_codes = array_intersect($this->codes, self::getMapExistedCodes());
    }

    /**
     * @return boolean
     */
    private function handleFailTransfer()
    {
        // get transfers only if they have in existed passengers AND Type FAIL
        return $this->featuresFlight->has_fail_transfers = EdbPassengerTransfer::find()
            ->where(['flight_id' => $this->flight->id])
            ->andWhere(['row_hash' => $this->row_hashes])
            ->andWhere(['type' => EdbPassengerTransfer::FAIL])
            ->exists();
    }

    /**
     * this method existed for storing to database all codes if they are not stored in database before.
     */

    private function storeDifferent()
    {
        if ($diffCodes = array_diff($this->codes, self::getMapAllCodes())) {
            foreach ($diffCodes as $code) {
                // put in all codes to use in the next iteration of handler
                array_push(self::$allCodes, $code);
                $newCode = new FlightPassengerAllCodes(['code' => $code]);
                $newCode->is_existed = in_array($code, self::getMapExistedCodes());
                $newCode->save(false);
            }
        }
    }

    public static function getMapAllCodes(): array
    {
        return (self::$allCodes !== null) ? self::$allCodes : self::$allCodes = FlightPassengerAllCodes::find()->select('code')->column();
    }

    public static function getMapExistedCodes(): array
    {
        return (self::$existedCodes !== null) ? self::$existedCodes : self::$existedCodes = EdbPassengerCode::find()->select('code')->column();
    }

    private function collectPosCodes()
    {
        $crew_pos = [];
        /** @var Flight $flight */
        if ($flight = $this->flight) {
            if ($flight->crew) {
                /** @var FlightLegCrew $crew */
                foreach ($flight->crew as $crew) {
                    if ($crew->getPositionId() == FlightLegCrew::POSITION_PU) {
                        $crew_pos[] = FlightSearch::PU;
                    }
                    if ($crew->getPositionId() == FlightLegCrew::POSITION_FI) {
                        $crew_pos[] = FlightSearch::FI;
                    }
                    if ($crew->getPositionId() == FlightLegCrew::POSITION_PU_TRAINEE) {
                        $crew_pos[] = FlightSearch::FA;
                    }
                }
            }
        }

        $this->featuresFlight->crew_pos = array_unique($crew_pos);
        if ($this->featuresFlight->crew_pos) {
            ExtendedLogger::storeLog("ADDED CREW POS");
            ExtendedLogger::storeLog($this->featuresFlight->crew_pos);
        }
    }


}
