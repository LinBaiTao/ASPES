<?php
/**
 * Project: aspes.msc
 * Author:  Chukwuemeka Nwobodo (jcnwobodo@gmail.com)
 * Date:    9/16/2016
 * Time:    8:20 PM
 **/

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Commons\SingletonInstance;
use App\Http\Controllers\Controller;
use App\Models\Evaluator;
use App\Models\Exercise;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Class ExerciseController
 *
 * @package App\Http\Controllers
 */
class ExerciseController extends Controller
{
    use SingletonInstance;

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExerciseList(Request $request)
    {
        $exercises = Exercise::all();
        $total = $exercises->count();
        parseListRange($request, $exercises->count(), $from, $to, 200);
        $list = $exercises->take($to - $from + 1); //adding 1 makes the range inclusive

        return ['net_total' => $total, 'list' => $list];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getResultsList(Request $request)
    {
        /**
         * @var Collection $exercises
         */
        $exercises = Exercise::where('state', Exercise::IS_PUBLISHED)->get();
        $total = $exercises->count();
        parseListRange($request, $exercises->count(), $from, $to, 200);
        $list = $exercises->take($to - $from + 1); //adding 1 makes the range inclusive

        return ['net_total' => $total, 'list' => $list];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getLiveList(Request $request)
    {
        /**
         * @var Collection $exercises
         */
        $exercises = Exercise::allLive()->get();
        $total = $exercises->count();
        parseListRange($request, $exercises->count(), $from, $to, 200);
        $list = $exercises->take($to - $from + 1); //adding 1 makes the range inclusive

        return ['net_total' => $total, 'list' => $list];
    }

    /**
     * @param Exercise $exercise
     *
     * @return array
     */
    public function getExerciseRelations(Exercise $exercise)
    {
        return [
            'status' => true,
            'object' => [
                'id'        => $exercise->id,
                'main'      => $exercise,
                'relations' => [
                    'courseComments'     => $exercise->courseComments()->getResults()->sortByDesc('grade'),
                    'instructorComments' => $exercise->instructorComments()->getResults()->sortByDesc('grade'),
                    'courseFactors'      => $exercise->courseFactors,
                    'instructorFactors'  => $exercise->instructorFactors,
                    'subjects'           => $exercise->subjects,
                    'evaluators'         => $exercise->evaluators,
                ],
            ],
        ];
    }

    /**
     * Initialize a new evaluation Exercise
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {
        $data = [];

        return $data;
    }

    /**
     * Set Factor Hierarchy for a given exercise
     *
     * @param Request $request
     *
     * @return string
     */
    public function setFactors(Request $request)
    {
        $data = [];

        return $data;
    }

    /**
     * Set evaluation comment set for an Exercise
     *
     * @param Request $request
     *
     * @return string
     */
    public function setComments(Request $request)
    {
        $data = [];

        return $data;
    }

    /**
     * Set exercise evaluators by passing an array of user ids and their respective evaluation roles through a post
     * request Evaluation roles could be
     *      1. Factor Comparison
     *      2. Subject Evaluation
     *
     * @param Request $request
     *
     * @return string
     */
    public function setEvaluators(Request $request)
    {
        $data = [];

        return $data;
    }

    /**
     * Set exercise subjects by passing an array of user ids through a post request
     *
     * @param Request $request
     *
     * @return array
     */
    public function setSubjects(Request $request)
    {
        $data = [];

        return $data;
    }

    /**
     * @param Evaluator $evaluator
     * @param Subject $subject
     * @param array $evaluations
     *
     * @return bool
     */
    public function saveSubjectEvaluation(Evaluator $evaluator, Subject $subject, array $evaluations)
    {
        try {
            $evaluator->evaluations()->where('subject_id', $subject->id)->delete();

            foreach ($evaluations as $factorId => $commentId) {
                $evaluator->evaluations()->create(['subject_id' => $subject->id, 'factor_id' => $factorId, 'comment_id' => $commentId]);
            }

            return true;
        }
        catch (\Exception $exception) {
            return false;
        }
    }
}
