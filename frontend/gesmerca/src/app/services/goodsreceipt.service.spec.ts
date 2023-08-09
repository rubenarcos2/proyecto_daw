import { TestBed } from '@angular/core/testing';

import { GoodsreceiptService } from './goodsreceipt.service';

describe('GoodsreceiptService', () => {
  let service: GoodsreceiptService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(GoodsreceiptService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
