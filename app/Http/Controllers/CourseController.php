<?php

namespace App\Http\Controllers;

use App\Services\CbrData\CbrDataService;
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
    public function get(string $targetCurrency, string $baseCurrency, string $date)
    {
        $course = $this->cbrDataService->getCourseOnDate($targetCurrency, $baseCurrency, $date);
        return response()->json([
            'course' => (float) number_format($course, 4),
        ]);
    }
}
