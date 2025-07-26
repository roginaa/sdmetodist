import { TestBed } from '@angular/core/testing';

import { SiswaionicService } from './siswaionic.service';

describe('SiswaionicService', () => {
  let service: SiswaionicService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(SiswaionicService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
