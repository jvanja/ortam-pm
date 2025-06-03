<?php

namespace App\Enums;

enum ProposalState: string {
  case Draft = 'draft';
  case Sent = 'sent';
  case Viewed = 'viewed';
  case Accepted = 'accepted';
  case Rejected = 'rejected';
  case Expired = 'expired';
  case Archived = 'archived';

  public function getLabel(): string {
    return match ($this) {
      self::Draft => __('Draft'),
      self::Sent => __('Sent'),
      self::Viewed => __('Viewed'),
      self::Accepted => __('Accepted'),
      self::Rejected => __('Rejected'),
      self::Expired => __('Expired'),
      self::Archived => __('Archived'),
    };
  }

  public function trans(): string {
    return $this->getLabel();
  }
}
