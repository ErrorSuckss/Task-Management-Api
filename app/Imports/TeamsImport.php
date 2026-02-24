<?php

namespace App\Imports;

use App\Models\Team;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeamsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $leader = null;

        if (!empty($row['team_leader'])) {
            $leader = User::where('name', $row['team_leader'])->where('role', 'team_leader')->first();
        }

        $team = new Team([
            'name' => $row['team_name'],
            'team_leader_id' => $leader?->id,
        ]);
        $team->save();

        if ($leader) {
            $leader->team_id = $team->id;
            $leader->save();
        }

        return $team;
    }
}
