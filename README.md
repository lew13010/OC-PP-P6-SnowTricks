# OC-PP-P6-SnowTricks
[Projet] Développez de A à Z le site communautaire SnowTricks

## REQUIREMENTS

* FOSUserBundle (users management)
* StofDoctrineExtensionsBundle (trick slug)
* VichUploaderBundle (upload image and avatar)
* AsseticBundle (assets management)

## INSTALLATION
### DATABASE

Create database:
```
$ php bin/console doctrine:database:create
```

Update table:
```
$ php bin/console doctrine:schema:update --force
```

Update data : 
```
$ php bin/console lew:load
```

### ASSETS
```
$ php bin/console assets:install --symlink 
```

# USE
For add, edit or delete a trick, and add a post in trick you must be logged in. 

Visitors can only view tricks and comments.