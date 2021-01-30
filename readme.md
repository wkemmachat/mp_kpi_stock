Change git url 

git remote -v 
git remote set-url origin https://github.com/wkemmachat/mp_kpi_stock_kem.git
change



php artisan make:model Role -mcr
php artisan make:migration create_role_user_table --create=role_user


#reset git 

git fetch origin

git reset --hard origin/master

git pull


#git push
git push origin master 


#Edit after clone and commit 

#Make Controller
php artisan make:controller ProductController
php artisan make:controller PhpSpreadSheetController

#Make Model
php artisan make:model Product

#Make Model migrade (-m) conroller (-c) 
php artisan make:model Todo -mcr

#Run Migration by name
php artisan migrate --path=/database/migrations/my_migration.php
php artisan make:model TransferInOut -mcr

#tinker
php artisan tinker
App\Product::find(1)->stock_real_time;
