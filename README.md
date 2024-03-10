# **Get Started**

## Introduction

**NexaORM** is a modern PHP Object-Relational Mapping (ORM) library designed to simplify database interactions and streamline the development of PHP applications. It provides a lightweight and intuitive way to manage database entities, relationships, and queries.

## Features

- **Attribute Annotations (Entities):** Define entity properties using expressive annotations, making it easy to map database columns to PHP properties.

- **Models:** Provide a foundation for representing and managing data objects within the application.

- **Simplified Query Building:** Construct database queries effortlessly with a simple and intuitive query builder.

- **Relationships:** Define and work with relationships between entities using straightforward annotations.

- **Auto-Migration:** NexaORM includes an auto-migration feature that simplifies the process of updating the database schema to match changes in entity definitions.

- **Flexible Configuration:** NexaORM is designed with flexibility in mind, allowing developers to adapt the library to their specific project requirements.

## Why NexaORM?

**Intelligent Auto-Generated Migrations:**

Say goodbye to tedious manual migration creation! NexaORM's intelligent auto-generation feature analyzes your entities and creates the necessary migrations to update your database schema accordingly. This powerful feature offers several benefits:

- **Save Time and Effort:** Eliminate the time-consuming and error-prone process of writing migrations manually.
- **Reduced Errors:** Ensure consistency between your entities and database schema, minimizing the risk of errors and data inconsistencies.
- **Efficient Development:** Streamline your development workflow by automating a crucial step in database management.

- **Effortless Database Management:** NexaORM takes database management to the next level by simplifying and automating various tasks:

- **Automatic Schema Updates:** Easily update your database schema to match your evolving entities without manual intervention.
- **Rollback Support:** Safely revert to a previous database version if necessary, providing a safety net in case of unexpected issues.
- **Version Control Integration:** Integrate your migrations with version control systems, enabling seamless collaboration and tracking of changes.

**Seamless Integration:**

NexaORM seamlessly integrates with your existing development environment:

* **Works with Any Framework:** Use NexaORM with any PHP framework, including Laravel, Symfony, and CodeIgniter.
* **Flexible Configuration:** Customize NexaORM's behavior to match your specific project requirements.
* **Extensible Architecture:** Extend NexaORM's functionality with custom plugins and modules.

**Community and Support:**

Join a vibrant community of developers and contributors who actively support NexaORM:

- **Detailed Documentation:** Access comprehensive documentation covering all aspects of NexaORM usage.
- **Responsive Support:** Get help and answers to your questions from the NexaORM community and maintainers.
- **Continuous Development:** Benefit from regular updates and new features driven by the active NexaORM community.

Choose NexaORM and unlock the power of intelligent auto-generated migrations, effortless database management, seamless integration, and a supportive community. Embrace a more efficient and error-free development workflow for your PHP applications.


## Installation

Use Composer to install the package:

```bash
composer require v1p3r75/nexa-orm
```

## Preview

```php

// Define entity

#[Entity]
class UserEntity
{

  #[PrimaryKey]
  #[SmallInt]
  #[AutoIncrement(true)]
  public int $id;

  #[Strings]
  #[DefaultValue('John')]
  public string $username;

  #[Number]
  #[ForeignKey(ProfileEntity::class, 'id', [Nexa::ON_DELETE => Nexa::CASCADE, Nexa::ON_UPDATE => Nexa::CASCADE])]
  #[Comment('user profile')]
  #[Nullable]
  public int $profile;

  #[DateAndTime]
  #[DefaultValue(Nexa::DATETIME_NOW)]
  public DateTime $created_at;
}
```

```php

// Create a model for database interation


use Nexa\Models\Model;
use Nexa\Test\Entities\UserEntity;

class User extends Model
{

  protected $entity = UserEntity::class;

}

User::insert(['username' => 'John Doe', 'email' => 'johndoe@test.com'])

```

## Authors

- [Fortunatus KIDJE (v1p3r75)](https://github.com/v1p3r75) - Main Developer

## Resources

- GitHub Repository: [https://github.com/v1p3r75/NexaORM](https://github.com/v1p3r75/NexaORM)
