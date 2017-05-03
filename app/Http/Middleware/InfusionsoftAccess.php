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
			$model = \App\Models\Course::findBySlugOrFail($slug);

			// This is cuz of the Vip users, redirect for the course,
			// but unlock all the content in Course
			if($model->is_tag_locked() && $request->user()->hasRole(['Vip']))
			{
				return redirect('/');
			}
		}elseif(array_key_exists('module', $parameters))
		{
			$slug = $parameters['module'];
			$model = \App\Models\Module::findBySlugOrFail($slug);
		}elseif(array_key_exists('lesson', $parameters))
		{
			$slug = $parameters['lesson'];
			$model = \App\Models\Lesson::findBySlugOrFail($slug);
		}elseif(array_key_exists('session', $parameters))
		{
			$slug = $parameters['session'];
			$model = \App\Models\Session::findBySlugOrFail($slug);
		}else{
			return $result;
		}

		if(is_role_admin())
		{
			return $result;
		}

		if($model->is_locked)
		{
			return redirect('/');
		}

        return $result;
    }
}