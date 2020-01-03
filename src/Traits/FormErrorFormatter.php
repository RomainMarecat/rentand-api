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

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

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
