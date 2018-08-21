#!/usr/bin/env bash

winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:migrations:migrate -n"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assets:install --symlink --env=dev"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assetic:dump --env=dev"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console cache:clear --env=dev"

winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/cache"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data app/logs"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/uploads"
winpty docker-compose -f docker-compose-dev.yml exec php bash -c "chown -R www-data:www-data web/cache"

result=$?

if [ $result -eq 0 ]
then
  echo "Update succeed!"
else
  echo "Update failed!" >&2
fi

exit $result