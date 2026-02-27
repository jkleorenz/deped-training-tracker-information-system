<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PersonnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personnel = [
            // ASUNCION INTEGRATED SCHOOL
            ['name' => 'TANIA A. ABECE', 'employee_id' => '4661304', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JUVY M. DIZON', 'employee_id' => '4659383', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'IRENE B. EGIDO', 'employee_id' => '424877', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JENNIFER B. GONZALES', 'employee_id' => '4654822', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JUNALITA P. INAJINES', 'employee_id' => '4554390', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'STEPHANIE G. MAITEM', 'employee_id' => '4654854', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'FLORENCIA M. MALBAS', 'employee_id' => '5805', 'designation' => 'Master Teacher I', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'EMERENCIO MARKINES', 'employee_id' => '6305231', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ALONA M. MONTON', 'employee_id' => '4641873', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'DENNIS MULAAN', 'employee_id' => '4662146', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'CESAR K. NACORDA', 'employee_id' => '4658790', 'designation' => 'SSTI', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'MYRNA C. OCAT', 'employee_id' => '6315271', 'designation' => 'Teacher I', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'RHEA ISABEL S. SALUDO', 'employee_id' => '4661307', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JONALOR J. TAGANA', 'employee_id' => '4661816', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ANEGEN L. TOLENTINO', 'employee_id' => '4587194', 'designation' => 'SSTIII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'SHERLINA VERANO', 'employee_id' => '4653169', 'designation' => 'MTI', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'LIGAYA P. ARBIOL', 'employee_id' => '11008', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'DARYL A. AVERGONZADO', 'employee_id' => '9135', 'designation' => 'Teacher II', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'MARIA CLARA A. BAGUHIN', 'employee_id' => '4808003', 'designation' => 'MT I', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ROSELLE L. BANGUIS', 'employee_id' => '9272', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'MELINDA L. CAGOROL', 'employee_id' => '4816440', 'designation' => 'Teacher II', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ARLINE A. CAPUYAN', 'employee_id' => '4249080', 'designation' => 'MT I', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'PHILINA G. DADAP', 'employee_id' => '4812812', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'HELGA C. ENTIA', 'employee_id' => '4247980', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ANITA G. ESTRADA', 'employee_id' => '3741', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ROSENDA A. GAMAYOT', 'employee_id' => '9616', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JANNA ESTELA A. JACOBE', 'employee_id' => '4654834', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'LESLIE LOPEZ', 'employee_id' => '4659342', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'PRETZEL MACUL', 'employee_id' => '6310100', 'designation' => 'Teacher II', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'MARRY ANN B. MAGALLANO', 'employee_id' => '4249084', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'DULCESIMA C. MALDO', 'employee_id' => '4814475', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JUNESSA A. MERCADO', 'employee_id' => '4660666', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'SHEILA S. OLARTE', 'employee_id' => '4529296', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ANALIZA V. PLASABAS', 'employee_id' => '4660006', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'SALVACION B. PORLAS', 'employee_id' => '4848755', 'designation' => 'Teacher III', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'AMOR O. ABANDO', 'employee_id' => '3401', 'designation' => 'PII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'FELY P. UY', 'employee_id' => '4530205', 'designation' => 'STII', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ANA MAE GRACE R. LASALA', 'employee_id' => '4661181', 'designation' => 'AO II', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'JONALYN PINGCAY', 'employee_id' => 'NEW', 'designation' => 'ADAS II', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'ANNA MARIE PALER', 'employee_id' => null, 'designation' => 'UTILITY', 'school' => 'ASUNCION INTEGRATED SCHOOL'],
            ['name' => 'FLORENCIO LARAGA', 'employee_id' => null, 'designation' => 'UTILITY', 'school' => 'ASUNCION INTEGRATED SCHOOL'],

            // BADIANG ELEMENTARY SCHOOL
            ['name' => 'RAMOS, LUCILA II M.', 'employee_id' => '4811833', 'designation' => 'SCHOOL PRINCIPAL I', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'ALMACEN, LILIBETH B.', 'employee_id' => '4657448', 'designation' => 'MASTER TEACHER II', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'MILLAN, NELA J.', 'employee_id' => '6270', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'PALIMA, JOY A.', 'employee_id' => '10574', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'BAYONETA, RUBIE P.', 'employee_id' => '9938', 'designation' => 'TEACHER II', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'BOHOL, IRMA J.', 'employee_id' => '4247092', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'BANDIBAS, NORLIE R.', 'employee_id' => '10570', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'PESCO, LEAH L.', 'employee_id' => '4249425', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'SAURO, CHARIE B.', 'employee_id' => '4554190', 'designation' => 'TEACHER III', 'school' => 'BADIANG ELEMENTARY SCHOOL'],
            ['name' => 'DALANGIN, CRISTINA E.', 'employee_id' => '6298087', 'designation' => 'ADMIN OFFICER II', 'school' => 'BADIANG ELEMENTARY SCHOOL'],

            // BACTUL I ELEMENTARY SCHOOL
            ['name' => 'JUEL S. LOBITE', 'employee_id' => '9801', 'designation' => 'Head Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],
            ['name' => 'REYNALEN D. ARBOLADORA', 'employee_id' => '4662197', 'designation' => 'Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],
            ['name' => 'MESEL C. CAUBE', 'employee_id' => '8221', 'designation' => 'Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],
            ['name' => 'MYRA C . MANCIO', 'employee_id' => '4659380', 'designation' => 'Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],
            ['name' => 'SUSANA A. CREDO', 'employee_id' => '4246699', 'designation' => 'Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],
            ['name' => 'RACHELLE ANNE N. MONDALA', 'employee_id' => '6299180', 'designation' => 'Teacher III', 'school' => 'BACTUL I ELEMENTARY SCHOOL'],

            // BATO I ELEMENTARY SCHOOL
            ['name' => 'ORDIZ, ESTRELLA B', 'employee_id' => '4661309', 'designation' => 'PRINCIPAL II', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'SULLA, ISABELITA L', 'employee_id' => '6308189', 'designation' => 'TEACHER III', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'MALAZARTE, MARLYN M', 'employee_id' => '4553800', 'designation' => 'TEACHER III', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'JURBAN, AZENITH G', 'employee_id' => '4247249', 'designation' => 'TEACHER III', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'PALOMA, AVALOR B', 'employee_id' => '6294147', 'designation' => 'TEACHER III', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'PALERO, JENILYN G', 'employee_id' => '6292132', 'designation' => 'TEACHER III', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'ORAG, BONIE MARK N.', 'employee_id' => '6318882', 'designation' => 'TEACHER I', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'MAMITES, MELANNIE G.', 'employee_id' => '5903284', 'designation' => 'TEACHER I', 'school' => 'BATO I ELEMENTARY SCHOOL'],
            ['name' => 'MARIA CECILIA C. ADANZA', 'employee_id' => '4660161', 'designation' => 'AO II', 'school' => 'BATO I ELEMENTARY SCHOOL'],

            // BATO II ELEMENTARY SCHOOL
            ['name' => 'LIEZL L. CABALLO', 'employee_id' => '428156562', 'designation' => 'T-III', 'school' => 'BATO II ELEMENTARY SCHOOL'],
            ['name' => 'MADELYN M. ORTIZ', 'employee_id' => '6310354', 'designation' => 'T-III', 'school' => 'BATO II ELEMENTARY SCHOOL'],
            ['name' => 'PATRICIO A. REMOJO, JR.', 'employee_id' => null, 'designation' => null, 'school' => 'BATO II ELEMENTARY SCHOOL'],
            ['name' => 'CHERRY ANN A. TELEN', 'employee_id' => null, 'designation' => null, 'school' => 'BATO II ELEMENTARY SCHOOL'],
            ['name' => 'MELESSA M. FERNANDEZ', 'employee_id' => null, 'designation' => 'T-VI', 'school' => 'BATO II ELEMENTARY SCHOOL'],
            ['name' => 'NYMPHA C. GESULTUR', 'employee_id' => null, 'designation' => 'AO-II', 'school' => 'BATO II ELEMENTARY SCHOOL'],

            // BILIBOL ELEMENTARY SCHOOL
            ['name' => 'ACERO, DANTE P.', 'employee_id' => '6053', 'designation' => 'TEACHER III', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'ADOBAS, EDWINA B.', 'employee_id' => '4810805', 'designation' => 'MASTER TEACHER I', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'CARDOZA, SHIELA R.', 'employee_id' => '5026148', 'designation' => 'PRINCIPAL III', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'CORTEL, JENNY A.', 'employee_id' => '4848757', 'designation' => 'TEACHER III', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'ESPERE, JUDY LYNN E', 'employee_id' => '6298078', 'designation' => 'AO II', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'GAYO, JENNEFER C.', 'employee_id' => '6299642', 'designation' => 'TEACHER II', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'MAIKE, TESSIE P.', 'employee_id' => '6304585', 'designation' => 'TEACHER II', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'PAL, CARA JEAN E.', 'employee_id' => '6306514', 'designation' => 'TEACHER III', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'RENDON, ABBY MAE LUCINO', 'employee_id' => '6319137', 'designation' => 'TEACHER I', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
            ['name' => 'SABANDAL, ELLAN Q.', 'employee_id' => '4660665', 'designation' => 'TEACHER III', 'school' => 'BILIBOL ELEMENTARY SCHOOL'],
        ];

        foreach ($personnel as $person) {
            $email = $this->generateEmail($person['name']);
            
            User::create([
                'name' => $person['name'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'personnel',
                'employee_id' => $person['employee_id'],
                'designation' => $person['designation'],
                'department' => null,
                'school' => $person['school'],
            ]);
        }
    }

    /**
     * Generate email from name
     */
    private function generateEmail(string $name): string
    {
        // Remove extra spaces and convert to lowercase
        $name = strtolower(trim($name));
        
        // Remove suffixes like JR., SR., II, III, etc.
        $name = preg_replace('/,\s*(jr|sr|ii|iii|iv|v)\.?/i', '', $name);
        
        // Remove middle initial (single letter followed by dot)
        $name = preg_replace('/\s+[a-z]\./', '', $name);
        
        // Replace spaces with dots
        $emailName = str_replace(' ', '.', $name);
        
        // Remove any remaining special characters
        $emailName = preg_replace('/[^a-z.]/', '', $emailName);
        
        // Remove multiple dots
        $emailName = preg_replace('/\.+/', '.', $emailName);
        
        // Trim dots from ends
        $emailName = trim($emailName, '.');
        
        return $emailName . '@deped.gov.ph';
    }
}
