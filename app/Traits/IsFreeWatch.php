<?php


namespace App\Traits;

/**
 * Trait IsFreeWatch
 *
 * @package App\Traits
 */
trait IsFreeWatch
{

    public function isCourseMustWatch()
    {
        return $this->course->must_watch;
    }

    public function isCompleteVideoFeatureOn()
    {
        return $this->course->complete_feature;
    }
}