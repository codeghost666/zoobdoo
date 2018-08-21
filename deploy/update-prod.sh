#!/usr/bin/env bash

docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:migrations:migrate -n"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assets:install --symlink --env=prod"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assetic:dump --env=prod"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console cache:clear --env=prod"

docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/cache"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/logs"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/uploads"
docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/cache"

result=$?

if [ $result -eq 0 ]
then
  echo "Update succeed!"
else
  echo "Update failed!" >&2
fi

exit $result