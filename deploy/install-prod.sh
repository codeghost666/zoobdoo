#!/usr/bin/env bash

docker-compose -f docker-compose-dev.yml exec php bash -c "composer install"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:database:create --if-not-exists"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:migrations:migrate -n"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assets:install --symlink --env=prod"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assetic:dump --env=prod"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console cache:clear --env=prod"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:fixtures:load --append"

docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/cache"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/logs"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/uploads"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/cache"

result=$?

#curl https://api.stripe.com/v1/plans \
#   -u ${STRIPE_SECRET_KEY}: \
#   -d amount=1 \
#   -d interval=year \
#   -d name="Base yearly plan" \
#   -d currency=usd \
#   -d id=base_yearly_plan

#curl https://api.stripe.com/v1/plans \
#   -u ${STRIPE_SECRET_KEY}: \
#   -d amount=1 \
#   -d interval=month \
#   -d name="Monthly plan" \
#   -d currency=usd \
#   -d id=monthly_plan

if [ $result -eq 0 ]
then
  echo "Deploy succeed!"
else
  echo "Deploy failed!" >&2
fi

exit $result