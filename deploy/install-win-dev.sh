#!/usr/bin/env bash

winpty docker-compose -f docker-compose-dev.yml exec php bash -c "composer install"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:database:create --if-not-exists"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:migrations:migrate -n"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assets:install --symlink --env=dev"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assetic:dump --env=dev"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console cache:clear --env=dev"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:fixtures:load --append"

winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/cache"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/logs"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/uploads"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/cache"

result=$?

curl https://api.stripe.com/v1/plans \
   -u ${STRIPE_SECRET_KEY}: \
   -d amount=1 \
   -d interval=year \
   -d name="Base yearly plan" \
   -d currency=usd \
   -d id=base_yearly_plan

curl https://api.stripe.com/v1/plans \
   -u ${STRIPE_SECRET_KEY}: \
   -d amount=1 \
   -d interval=month \
   -d name="Monthly plan" \
   -d currency=usd \
   -d id=monthly_plan

if [ $result -eq 0 ]
then
  echo "Deploy succeed!"
else
  echo "Deploy failed!" >&2
fi

exit $result