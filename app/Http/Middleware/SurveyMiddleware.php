<?php

namespace App\Http\Middleware;

use DB;
use Closure;

class SurveyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = $next($request);

        $parameters = $request->route()->parameters;

		if(array_key_exists('course', $parameters))
		{
			$slug = $parameters['course'];
			$model = \App\Models\Course::findBySlugOrFail($slug);
		}else{
			return $result;
		}

		$popupCheck = DB::table('surveys')->where('user_id', $request->user()->id)->get()->toArray();
		if($model->slug == 'ask-masterclass' && empty($popupCheck)) {
			return redirect()->route('single.course', $model->slug);
        }else{
            return $result;
        }
    }
}
