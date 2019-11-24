<?php

namespace Tests\Feature;

use App\Models\ApplicationForm\ApplicationForm;
use App\Models\ApplicationForm\ApplicationFormRow;
use App\User;
use Artisan;
use Config;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use TestCase;

/**
 * Class CreateApplicationFormTest
 * @package Tests\Feature
 */
class CreateApplicationFormTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    const URL = 'beheer/applicationForms';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $user = factory(User::class)->create();

        $user->roles()->attach(Config::get('constants.Content_administrator'));
        $this->be($user);

        session()->start();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /** @test */
    public function create_application_form_as_content_administrator(): void
    {
        $body = [
            'nl_name' => 'test nl name',
            'en_name' => 'test en name',
            'rows' => [
                [
                    'nl_name' => 'Vraag 1',
                    'en_name' => 'Question 1',
                    'type' => ApplicationFormRow::FORM_TYPE_NUMBER,
                ],
                [
                    'nl_name' => 'Vraag 2',
                    'en_name' => 'Question 2',
                    'type' => ApplicationFormRow::FORM_TYPE_CHECK_BOX,
                    'required' => true
                ]
            ]
        ];

        $response = $this->post(self::URL, $body);

        $response->assertStatus(302);

        $applicationForm = ApplicationForm::all()->last();

        $this->assertNotNull($applicationForm);
        $this->assertApplicationForm($applicationForm, $body);
    }

    /** @test */
    public function create_application_form_as_administrator_should_return_403(): void
    {
        $this->user->roles()->sync([Config::get('constants.Administrator')]);

        $body = [
            'nl_name' => 'test nl name',
            'en_name' => 'test en name',
            'rows' => [
                [
                    'nl_name' => 'Vraag 1',
                    'en_name' => 'Question 1',
                    'type' => ApplicationFormRow::FORM_TYPE_NUMBER,
                ],
                [
                    'nl_name' => 'Vraag 2',
                    'en_name' => 'Question 2',
                    'type' => ApplicationFormRow::FORM_TYPE_CHECK_BOX,
                    'required' => true
                ]
            ]
        ];

        $response = $this->post(self::URL, $body);

        $response->assertStatus(403);
    }

    /** @test */
    public function create_application_form_as_certificate_administrator_should_return_403(): void
    {
        $this->user->roles()->sync([Config::get('constants.Certificate_administrator')]);

        $body = [
            'nl_name' => 'test nl name',
            'en_name' => 'test en name',
            'rows' => [
                [
                    'nl_name' => 'Vraag 1',
                    'en_name' => 'Question 1',
                    'type' => ApplicationFormRow::FORM_TYPE_NUMBER,
                ],
                [
                    'nl_name' => 'Vraag 2',
                    'en_name' => 'Question 2',
                    'type' => ApplicationFormRow::FORM_TYPE_CHECK_BOX,
                    'required' => true
                ]
            ]
        ];

        $response = $this->post(self::URL, $body);

        $response->assertStatus(403);
    }

    /**
     * @param ApplicationForm $applicationForm
     * @param array $data
     */
    private function assertApplicationForm(ApplicationForm $applicationForm, array $data){
        $this->assertEquals($data['nl_name'], $applicationForm->applicationFormName->NL_text);
        $this->assertEquals($data['en_name'], $applicationForm->applicationFormName->EN_text);
        $this->assertCount(count($data['rows']), $applicationForm->applicationFormRows);

        for($i=0; $i < count($applicationForm->applicationFormRows); $i++) {
            $rowData = $data['rows'][$i];
            $row = $applicationForm->applicationFormRows[$i];

            $this->assertEquals($rowData['nl_name'], $row->applicationFormRowName->NL_text);
            $this->assertEquals($rowData['en_name'], $row->applicationFormRowName->EN_text);
            $this->assertEquals($rowData['type'], $row->type);
            $this->assertEquals(array_key_exists('required', $rowData), $row->required);
        }
    }
}
