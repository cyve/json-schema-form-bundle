# UserBundle

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/user-bundle
```

## Configuration

```php
// config/bundles.php
return [
    ...
    Cyve\UserBundle\CyveUserBundle::class => ['all' => true],
];
```
```yaml
// config/routes/cyve_user.yaml
cyve_user:
    resource: "@CyveUserBundle/Resources/config/routing.yaml"
```
```yaml
// config/packages/cyve_user.yaml
cyve_user:
    user_class: App\Entity\User
```
```yaml
// config/packages/security.yaml
security:
    encoders:
        Symfony\Component\Security\Core\User\UserInterface: bcrypt
    providers:
        cyve_user_provider:
            id: Cyve\UserBundle\Security\UserProvider
    firewalls:
        main:
            anonymous: true
            guard:
                authenticators:
                    - Cyve\UserBundle\Security\LoginFormAuthenticator
    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/recovery, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, roles: IS_AUTHENTICATED_REMEMBERED }
    role_hierarchy:
        ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ALLOWED_TO_SWITCH]
```
```php
// src/Entity/User.php
<?php

namespace App\Entity;

use Cyve\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Le nom d'utilisateur {{ value }} exite déjà")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;
}
```
```php
// src/Repository/UserRepository.php
<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
}
```
