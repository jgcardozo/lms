<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SessionTypeScope implements Scope
{
	protected $type;

	/**
	 * OnlyCoachingCallsScope constructor.
	 *
	 * @param string $type
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->where('type', $this->type);
	}
}