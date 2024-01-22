import { TestBed } from '@angular/core/testing';
import { CanDeactivateFn } from '@angular/router';

import { CanDeactivateBlockNavigationIfChange } from './block-navigation-if-change.guard';

describe('blockNavigationIfChangeGuard', () => {
  let guard: CanDeactivateBlockNavigationIfChange;

  beforeEach(() => {
    guard = TestBed.inject(CanDeactivateBlockNavigationIfChange);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});
