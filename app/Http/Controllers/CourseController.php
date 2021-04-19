<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CbrData\CbrDataService;
use App\Services\CbrData\Exceptions\CbrDataExternalException;
use App\Services\CbrData\Exceptions\CbrDataInternalException;
use Illuminate\Http\JsonResponse;

/**
 * Class CourseController
 * @package App\Http\Controllers
 */
class CourseController extends Controller
{
    /**
     * @var CbrDataService
     */
    private $cbrDataService;

    /**
     * CourseController constructor.
     * @param CbrDataService $cbrDataService
     */
    public function __construct(CbrDataService $cbrDataService)
    {
        $this->cbrDataService = $cbrDataService;
    }

    /**
     * Returns target currency course by base currency on specific date
     * @param string $targetCurrency ISO char code
     * @param string $baseCurrency ISO char code
     * @param string $date YYYY-MM-DD
     * @return JsonResponse
     */
    public function getCourse(string $targetCurrency, string $baseCurrency, string $date): JsonResponse
    {
        try {
            $serviceResult = $this->cbrDataService->getCourseOnDate($targetCurrency, $baseCurrency, $date);
            return response()->json([
                'course' => (float) number_format($serviceResult->getCourse(), 4, '.', ''),
                'tradeDay' => $serviceResult->getTradeDay()->format('d.m.Y'),
                'courseDiff' => (float) number_format($serviceResult->getPreviousTradeDayCourseDiff(), 4, '.', ''),
                'previousTradeDay' => $serviceResult->getPreviousTradeDay()->format('d.m.Y'),
            ]);
        } catch (CbrDataExternalException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (CbrDataInternalException $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Returns available currency ISO codes
     * @return JsonResponse
     */
    public function getAvailableCurrencies(): JsonResponse
    {
        return response()->json(['currencies' => $this->cbrDataService->getCurrencies()]);
    }
}
