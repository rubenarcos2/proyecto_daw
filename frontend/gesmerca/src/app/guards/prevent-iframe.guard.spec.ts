import { TestBed } from '@angular/core/testing';

import { CanDeactivateBlockNavigationIfChange } from './block-navigation-if-change.guard';

describe('preventIframeIfChangeGuard', () => {
  let guard: CanDeactivateBlockNavigationIfChange;

  beforeEach(() => {
    guard = TestBed.inject(CanDeactivateBlockNavigationIfChange);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});
