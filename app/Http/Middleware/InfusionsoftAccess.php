<?php

namespace App\Http\Middleware;

use Closure;

class InfusionsoftAccess
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
			$model = \App\Models\Course::findBySlug($slug);
		}elseif(array_key_exists('module', $parameters))
		{
			$slug = $parameters['module'];
			$model = \App\Models\Module::findBySlug($slug);
		}elseif(array_key_exists('lesson', $parameters))
		{
			$slug = $parameters['lesson'];
			$model = \App\Models\Lesson::findBySlug($slug);
		}elseif(array_key_exists('session', $parameters))
		{
			$slug = $parameters['session'];
			$model = \App\Models\Session::findBySlug($slug);
		}else{
			return $result;
		}

		if($model->is_locked)
		{
			abort(403);
		}

        return $result;
    }
}