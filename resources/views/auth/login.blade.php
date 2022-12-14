@extends('layouts.app')

@section('content')
<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <h2 class="heading-section">i-Keneya</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-wrap p-4 p-md-5">
              <div class="icon d-flex align-items-center justify-content-center">
                  <span class="fa fa-user-o"></span>
              </div>
              <h3 class="text-center mb-4">Mon compte</h3>
                    <form [formGroup]="loginForm" (ngSubmit)="onSubmit()" class="login-form">
                  <div class="form-group">
                      <input type="text" class="form-control rounded-left" formControlName="phone" placeholder="Numéro de téléphone" required>
                  </div>
            <div class="form-group d-flex">
              <input type="password" class="form-control rounded-left" formControlName="password" placeholder="Mot de passe" required>
            </div>
            <div class="form-group d-md-flex">

                <div class="w-50 text-md-right">
                    <a href="#">Mot de passe oublié?</a>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary rounded submit p-3 px-5">Se connecter</button>
            </div>
          </form>
        </div>
            </div>
        </div>
    </div>
</section>


@endsection
