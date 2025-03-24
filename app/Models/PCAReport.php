<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PCAReport extends Model {
  use HasFactory;
  //
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'occupation_of_the_property',
    'total_site_area',
    'surface_area_of_the_building',
    'occupation_of_the_building',
    'year_of_construction',
    'structure',
    'fondation',
    'building',
    'numbers_of_floors',
    'basement',
    'residential_units',
    'non_residential_units',
    'project_id',
    'client_id',
    'organization_id',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'total_site_area'              => 'integer',
    'surface_area_of_the_building' => 'integer',
    'occupation_of_the_building'   => 'decimal:2',
    'year_of_construction'         => 'integer',
    'numbers_of_floors'            => 'integer',
    'basement'                     => 'boolean',
    'residential_units'            => 'integer',
    'non_residential_units'        => 'integer',
  ];

  public function organization(): BelongsTo {
    return $this->belongsTo(Organization::class);
  }

}
