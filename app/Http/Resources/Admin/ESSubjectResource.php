<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ESSubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->subject_id,
            "type" => $this->subject_type,
            "tree" => $this->getLogSubject($this),
        ];
    }

    private function getLogSubject($log) {
        if(empty($log->activity) && empty($log->subject)) { return ""; }

        if($log->activity_id != 7) {
            if(empty($log->activity_id)) {
                if($log->subject !== null) {
                    if(empty($log->subject->title)) {
                        return $log->subject->name;
                    }
                    else {
                        $subject = "";
                        if($log->subject->module !== null) {
                            $subject .= "[ {$log->subject->module->title} ] ";
                        }
                        if($log->subject->lesson !== null) {
                            $subject .= "[ {$log->subject->lesson->module->title} - {$log->subject->lesson->title} ] ";
                        }

                        return $subject .= $log->subject->title;
                    }
                }
                else {
                    return $log->subject_type;
                }
            }
            else {
                return $log->activity->name;
            }
        }
        else {
            if($log->subject !== null) {
                if(empty($log->subject->title)) {
                    return $log->subject->name;
                }
                else {
                    $subject = "";
                    if($log->subject->module !== null) {
                        $subject .= "[ {$log->subject->module->title} ] ";
                    }
                    if($log->subject->lesson !== null) {
                        $subject .= "[ {$log->subject->lesson->module->title} - {$log->subject->lesson->title} ] ";
                    }

                    return $subject .= $log->subject->title;
                }
            }
            else {
                if(empty($log->deleted)) {
                    if(empty($log->deleted_user)) {
                        return $log->subject_type;
                    }
                    else {
                        return $log->deleted_user;
                    }
                }
                else {
                    return $log->deleted;
                }
            }
        }
    }
}
