<?php

namespace RefactoringGuru\AbstractFactory\RealWorld;

/**
 * EN: Abstract Factory Design Pattern
 *
 * Intent: Provide an interface for creating families of related or dependent
 * objects without specifying their concrete classes.
 *
 * Example: In this example, the Abstract Factory pattern provides an
 * infrastructure for creating various types of templates for different elements
 * of a web page.
 *
 * A web application can support different rendering engines at the same time,
 * but only if its classes are independent of concrete classes of rendering
 * engines. Hence, the application's objects must communicate with template
 * objects only via their abstract interfaces. Your code should not create the
 * template objects directly, but delegate the creation to special factory
 * objects. Finally, your code should not depend on the factory objects either
 * but work with them via abstract factory interface.
 *
 * As a result, you will be able to provide the app with the factory object that
 * corresponds to one of the rendering engines. All templates, created in the
 * app, will be created by that factory and their type will match the type of
 * the factory. If you decide to change the rendering engine, you'll be able to
 * pass a new factory to the client code, without breaking any existing code.
 *
 * RU: Паттерн Абстрактная Фабрика
 *
 * Назначение: Предоставляет интерфейс для создания семейств связанных или
 * зависимых объектов, без привязки к их конкретным классам.
 *
 * Пример: В этом примере паттерн Абстрактная Фабрика предоставляет
 * инфраструктуру для создания нескольких разновидностей шаблонов для одних и
 * тех же элементов веб-страницы.
 *
 * Чтобы веб-приложение могло поддерживать сразу несколько разных движков
 * рендеринга страниц, его классы должны работать с шаблонами только через
 * интерфейсы, не привязываясь к конкретным классам. Чтобы этого достичь,
 * объекты приложения не должны создавать шаблоны напрямую, а поручать создание
 * спецальным объектам-фабрикам, с которыми тоже надо работать через абстрактный
 * интерфейс.
 *
 * Благодаря этому, вы можете подать в приложение фабрику, соотвествующую одному
 * из движков рендеринга, зная что с этого момента, все шаблоны будут
 * порождаться именно этой фабрикой, и будут соотвествовать движку рендеринга
 * этой фабрики. Если вы захотите сменить движок рендеринга, то всё что нужно
 * будет сделать — это подать в приложение объект фабрики другого типа и ничего
 * при этом не сломается.
 */

/**
 * The Abstract Factory interface declares creation methods for each distinct
 * product type.
 */
interface TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate;

    public function createPageTemplate(): PageTemplate;
}

/**
 * Each Concrete Factory corresponds to a specific variant (or family) of
 * products.
 *
 * This Concrete Factory creates Twig templates.
 */
class TwigTemplateFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new TwigTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new TwigPageTemplate();
    }
}

/**
 * And this Concrete Factory creates only Blade templates.
 */
class BladeFactory implements TemplateFactory
{
    public function createTitleTemplate(): TitleTemplate
    {
        return new BladeTitleTemplate();
    }

    public function createPageTemplate(): PageTemplate
    {
        return new BladePageTemplate();
    }
}

/**
 * Each distinct product type should have a separate interface. All variants of
 * the product must follow the same interface.
 *
 * For instance, this Abstract Product interface describes the behavior of page
 * title templates.
 */
interface TitleTemplate
{
    public function render(): string;
}

/**
 * This Concrete Product provides Twig page title templates.
 */
class TwigTitleTemplate implements TitleTemplate
{
    public function render(): string
    {
        return "<h1>{{ title }}</h1>";
    }
}

/**
 * And this Concrete Product provides Blade page title templates.
 */
class BladeTitleTemplate implements TitleTemplate
{
    public function render(): string
    {
        return "<h1><?php print(\$title) ?></h1>";
    }
}

/**
 * This is another Abstract Product type, which describes whole page templates.
 */
interface PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string;
}

/**
 * The Twig variant of the whole page templates.
 */
class TwigPageTemplate implements PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string
    {
        $title = $titleTemplate->render();
        return <<<EOF
<div class="page">
  $title
  <article class="content">{{ content }}</article>
</div>
EOF;
    }
}

/**
 * The Blade variant of the whole page templates.
 */
class BladePageTemplate implements PageTemplate
{
    public function render(TitleTemplate $titleTemplate): string
    {
        $title = $titleTemplate->render();
        return <<<EOF
<div class="page">
  $title
  <article class="content"><?php print(\$content) ?></article>
</div>
EOF;
    }
}

/**
 * The client code. Note that it accepts the Abstract Factory class as the
 * parameter, which allows the client to work with any concrete factory type.
 */
function templateRenderer(TemplateFactory $factory)
{
    $titleTemplate = $factory->createTitleTemplate();
    $pageTemplate = $factory->createPageTemplate();

    print($pageTemplate->render($titleTemplate));
}

/**
 * Somewhere in other parts of the app. The client code can accept factory
 * objects of any type.
 */
print("Testing rendering with the Twig factory:\n");
templateRenderer(new TwigTemplateFactory());
print("\n\n");

print("Testing rendering with the Blade factory:\n");
templateRenderer(new BladeFactory());
