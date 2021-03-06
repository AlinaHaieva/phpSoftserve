<?php

require_once "Team.php";
require_once "specialists/Candidate.php";
require_once "HRteam.php";

class ITcompany
{
    private $candidates = [];
    private $teams = [];

    public function getCandidatesObjectsArray() {
        foreach (tasksDatabase::getAllCandidatesFromDB() as $row) {
            $name = $row["name"];
            $wantedSalary = $row["wanted_salary"];
            $cv = $row["cv"];

            $this->candidates[] = new Candidate($name, $wantedSalary, $cv);
        }
    }

    public function getCandidates()
    {
        $this->getCandidatesObjectsArray();
        return $this->candidates;
    }

    public function getTeamsObjectsArray() {
        foreach (tasksDatabase::getAllTeamsFromDB() as $row) {
            $teamName = $row["name"];
            $project = $row["project"];

            $this->teams[] = new Team($teamName, $project);
        }
    }

    public function getTeams()
    {
        return $this->teams;
    }

    public function hire()
    {
        $this->getTeamsObjectsArray();
        $this->chooseTeam();
    }

    private function chooseTeam()
    {
        foreach ($this->teams as $team) {
            $team->getTeamMembersObjectsArray();
            $team->getTeamNeedsObjectsArray();
            if (!$team->isComplete()) {
                $this->chooseRecruter($team);
            }
        }
    }

    private function chooseRecruter($team)
    {
        $hrTeam = new HRteam();
        $allCandidates = $this->candidates;

        foreach ($team->getTeamNeeds() as $neededSpecialist => $neededQuantity) {
            for ($i = 0; $i < $neededQuantity; $i++) {
                $recruter = $hrTeam->chooseRecruter($neededSpecialist);
                $recruter->getSpecialist($allCandidates, $neededSpecialist, $team);
            }
        }
    }

    public function getFun()
    {
        $teamInWork = [];
        foreach ($this->teams as $team) {
            $teamInWork[] = $team->teamName . $team->doJob();
        }
        return $teamInWork;
    }
}
