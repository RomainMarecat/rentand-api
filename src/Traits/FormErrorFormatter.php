<?php

namespace App\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

trait FormErrorFormatter
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public static function getErrors(FormInterface $form)
    {
        $errors = [];

        $errors = self::getCurrentErrors($form, $errors);
        $errors = self::getChildErrors($form, $errors);

        return $errors;
    }

    private static function getCurrentErrors(FormInterface $form, array $errors)
    {
        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
                continue;
            }
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    private static function getChildErrors(FormInterface $form, array $errors)
    {
        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                $childError = self::getErrors($child);
                if (!empty($childError)) {
                    $errors[$child->getName()] = $childError;
                }
            }
        }

        return $errors;
    }

    /**
     * Renvoie une réponse formattée pour gestion via framework js
     *
     * @param FormInterface $form
     *
     * @return JsonResponse
     */
    public static function getErrorsAsJsonResponse(FormInterface $form)
    {
        return new JsonResponse([
            'form_errors' => self::getErrors($form)
        ], 400);
    }
}
