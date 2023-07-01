import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Config } from 'src/app/models/config';
import { ConfigService } from 'src/app/services/config.service';

@Component({
  selector: 'app-edit',
  templateUrl: './edit.component.html',
  styleUrls: ['./edit.component.css']
})
export class ConfigGeneralEditComponent implements OnInit {

  configForm!: FormGroup;
  dataForm!: FormData;
  returnUrl!: string;
  isSubmitted: boolean = false;
  private _config?: Config;

  constructor(
    private formBuilder: FormBuilder,
    private configService: ConfigService,
    private toastr: ToastrService,
    private route: ActivatedRoute,
    private router: Router
  ) { }

  ngOnInit(): void {
    let id;
    this.dataForm = new FormData(); 
    this.route.params.subscribe( param => id = parseInt(param['id']));
    this.configService.getById(id)
      .subscribe({
          next: (result) => {
              this._config = result;
              this.configForm = this.formBuilder.group({
                name: [this.config?.name, [Validators.required, Validators.minLength(3)]],
                title: [this.config?.title, [Validators.required, Validators.minLength(3)]],
                description: [this.config?.description, [Validators.required, Validators.minLength(3)]],                
                domain: [this.config?.domain, Validators.required],                
                value: [this.config?.value === 'true' ? true : false, Validators.required]
              });
              this.returnUrl = this.route.snapshot.queryParams['returnUrl'];
          },
          error: (error) => {
              this.toastr.error(error ? error : "Operación no autorizada");
          }
      });   
  }

  onSubmit() {
    this.isSubmitted = true;
    this.dataForm.append("name", this.configForm.get('name')?.value);
    this.dataForm.append("title", this.configForm.get('title')?.value);
    this.dataForm.append("description", this.configForm.get('description')?.value);    
    this.dataForm.append("domain", this.configForm.get('domain')?.value);
    this.dataForm.append("value", this.configForm.get('value')?.value);
    
    this.configService.update(this.dataForm).subscribe({
      next: (result) => {
        let res = JSON.parse(JSON.stringify(result));
        res.error ? this.toastr.error(res.error) : this.toastr.success(res.message);
        this.router.navigate([this.returnUrl || '/config/general']);
      },
      error: (error) => {
        console.log(error);
        this.toastr.error(error.error ? error.error : "No se puede conectar con el servidor");
      }
    })
    .add(() => {
      this.isSubmitted = false;
    });
  }

  onChangeInput(event:any){ //TODO
    let input = event.target.id;
    this.isSubmitted = true;
    switch (input) {
      case 'inputName':
        this.isSubmitted = this.configForm.get(input)?.value !== this.config?.name;
        break;
      case 'inputDescription':
        this.isSubmitted = this.configForm.get(input)?.value !== this.config?.description;
        break;
      case 'inputTitle':
        this.isSubmitted = this.configForm.get(input)?.value !== this.config?.title;
        break;
      case 'inputDomain':
          this.isSubmitted = this.configForm.get(input)?.value !== this.config?.domain;
          break;
      case 'inputValue':
        this.isSubmitted = this.configForm.get(input)?.value !== this.config?.value;
        break;
    }
  }

  get configFormControls() {
    return this.configForm.controls;
  }

  get config(){
    return this._config;
  }
}
