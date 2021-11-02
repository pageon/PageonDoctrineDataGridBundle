How to use the Pageon Doctrine Data Grid Bundle
============

All the configuration happens on the entity

If you want to display a property of the entity you should add the `Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn` attribute to it. You can only filter or sort on properties.

Since you sometimes might want to run some custom code to manipulate the output first you can also display the result of a method on your entity by adding the If you want to display a property of the entity you should add the `Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn` attribute to it.

Lastly if you want to display a button to go to an edit page for instance you need the `Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn` attribute. It has the option to pass a callback so you can add extra parameters that are specific to the row to the route, like an id for example.

After configuration all you need to do is pass the entity to the `Pageon\DoctrineDataGridBundle\DataGrid\DataGridFactory::forEntity` method.
The output of that can be rendered in twig like this `{{ pageon_datagrid(myDataGrid) }}`

If you want more control over the rendering you can always overwrite the `@PageonDoctrineDataGridBundle/dataGrid.html.twig` template.
The template is made for bootstrap 5 so if you use the default it would be best to use the bootstrap 5 templates in the paginator config as well
```yaml
knp_paginator:
  ...         # default number of items per page
  template:
    pagination: '@KnpPaginator/Pagination/bootstrap_v5_pagination.html.twig'
    sortable: '@KnpPaginator/Pagination/bootstrap_v5_fa_sortable_link.html.twig'
    filtration: '@KnpPaginator/Pagination/bootstrap_v5_filtration.html.twig'
```

### Example

```php
<?php

namespace App\Entity\UserGroup;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Pageon\DoctrineDataGridBundle\Attribute\DataGrid;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridActionColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridMethodColumn;
use Pageon\DoctrineDataGridBundle\Attribute\DataGridPropertyColumn;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 */
#[UniqueEntity(fields: ['name'])]
#[DataGrid('UserGroup')]
#[DataGridActionColumn(
    route: 'backend',
    routeAttributes: [
        'module' => 'backend',
        'action' => 'group_edit'
    ],
    label: 'lbl.Edit',
    routeAttributesCallback: [self::class, 'dataGridEditLinkCallback'])
]
class UserGroup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[DataGridPropertyColumn(sortable: true, filterable: true)]
    private string $name;

    /**
     * @var Collection<int, User>|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="userGroups")
     */
    protected $users;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->users = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addUser(User $user): void
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
        $user->addUserGroup($this);
    }

    public function removeUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->removeElement($user);
        $user->removeUserGroup($this);
    }

    /** @return Collection<int, User>|User[] */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    #[DataGridMethodColumn()]
    public function getUserCount(): int
    {
        return $this->users->count();
    }

    public static function dataGridEditLinkCallback(UserGroup $userGroup): array
    {
        return ['id' => $userGroup->getId()];
    }
}
```
