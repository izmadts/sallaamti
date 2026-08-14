<?php

namespace App\Http\Controllers\Concerns;

// Generic session-persisted, multi-step "save and continue later" form
// plumbing. Consuming controllers set $wizardKey (a unique session
// namespace) and $wizardStepFields (ordered [step => [field names]]) and
// get step storage/progression/completion tracking for free.
trait HasWizardSteps
{
    protected function wizardSessionKey(): string
    {
        return "wizard.{$this->wizardKey}";
    }

    protected function wizardSteps(): array
    {
        return array_keys($this->wizardStepFields);
    }

    protected function saveWizardStep(string $step, array $data): void
    {
        $key = $this->wizardSessionKey();

        session(["{$key}.steps.{$step}" => $data]);

        $completed = $this->wizardCompletedSteps();

        if (!in_array($step, $completed, true)) {
            $completed[] = $step;
            session(["{$key}.completed" => $completed]);
        }
    }

    protected function wizardStepData(string $step): array
    {
        return session("{$this->wizardSessionKey()}.steps.{$step}", []);
    }

    protected function wizardCompletedSteps(): array
    {
        return session("{$this->wizardSessionKey()}.completed", []);
    }

    protected function wizardAllData(): array
    {
        $data = [];

        foreach ($this->wizardSteps() as $step) {
            $data = array_merge($data, $this->wizardStepData($step));
        }

        return $data;
    }

    protected function firstIncompleteWizardStep(): ?string
    {
        $completed = $this->wizardCompletedSteps();

        foreach ($this->wizardSteps() as $step) {
            if (!in_array($step, $completed, true)) {
                return $step;
            }
        }

        return null;
    }

    protected function clearWizardSession(): void
    {
        session()->forget($this->wizardSessionKey());
    }
}
