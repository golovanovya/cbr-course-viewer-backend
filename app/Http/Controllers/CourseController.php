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
    public function get(string $targetCurrency, string $baseCurrency, string $date): JsonResponse
    {
        try {
            $course = $this->cbrDataService->getCourseOnDate($targetCurrency, $baseCurrency, $date);
            return response()->json(['course' => (float) number_format($course, 4)]);
        } catch (CbrDataExternalException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (CbrDataInternalException $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
