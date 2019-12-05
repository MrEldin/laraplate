<?php
/**
 *  @SWG\Definition (
 *      definition="EmptyResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object"
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ValidationErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="array",
 *          @SWG\Items(ref="#/definitions/ErrorsObjectV1")
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ErrorsObjectV1",
 *      required={"field_name"},
 *      @SWG\Property(
 *          property="field_name",
 *          description="field name",
 *          type="array",
 *          @SWG\Items(ref="#/definitions/ErrorsArrayV1")
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ErrorsArrayV1",
 *      required={"error"},
 *      type="string"
 * )
 *
 * @SWG\Definition (
 *      definition="UnauthorizedErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string",
 *          example="Unauthorized"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object",
 *          @SWG\Property(
 *          property="error",
 *          description="array with detailed errors",
 *          type="string",
 *          example="Unauthorized"
 *      ),
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer",
 *          example="401"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="BadRequestErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string",
 *          example="Bad Request"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object",
 *          @SWG\Property(
 *          property="error",
 *          description="array with detailed errors",
 *          type="string",
 *          example="Bad Request"
 *      ),
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer",
 *          example="400"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ForbiddenErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string",
 *          example="Forbidden"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object",
 *          @SWG\Property(
 *          property="error",
 *          description="array with detailed errors",
 *          type="string",
 *          example="Forbidden"
 *      ),
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer",
 *          example="403"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="NotFoundErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string",
 *          example="Not Found"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object",
 *          @SWG\Property(
 *          property="error",
 *          description="array with detailed errors",
 *          type="string",
 *          example="Not Found"
 *      ),
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer",
 *          example="404"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="NotAllowedErrorResponseV1",
 *      required={"message, errors, status_code, data"},
 *      type="object",
 *      @SWG\Property(
 *          property="message",
 *          description="error message",
 *          type="string",
 *          example="Method Not Allowed"
 *      ),
 *      @SWG\Property(
 *          property="errors",
 *          description="array with detailed errors",
 *          type="object",
 *          @SWG\Property(
 *          property="error",
 *          description="array with detailed errors",
 *          type="string",
 *          example="Method Not Allowed"
 *      ),
 *      ),
 *      @SWG\Property(
 *          property="status_code",
 *          description="http status code",
 *          type="integer",
 *          example="405"
 *     ),
 *     @SWG\Property(
 *          property="data",
 *          description="response data",
 *          type="object"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ArrayWithIdV1",
 *      required={"id"},
 *      @SWG\Property(
 *          property="id",
 *          description="object id",
 *          type="integer"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="CreateJobRequestJobsArrayV1",
 *      required={"id"},
 *      @SWG\Property(
 *          property="client_id",
 *          description="client id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="start_time",
 *          description="start time",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="end_time",
 *          description="end time",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="break_minutes",
 *          description="break minutes",
 *          type="integer",
 *      ),
 *      @SWG\Property(
 *          property="number_of_temps",
 *          description="number of temps",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="rate",
 *          description="ratee",
 *          type="number",
 *          format="double"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="SearchTempsJobsArrayV1",
 *      required={"id"},
 *      @SWG\Property(
 *          property="client_id",
 *          description="client id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="start_time",
 *          description="start time",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="end_time",
 *          description="end time",
 *          type="string",
 *          format="date"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="ArrayV1",
 *      type="string"
 * )
 *
 * @SWG\Definition (
 *      definition="JobTypesArrayV1",
 *      required={"id", "grade_id"},
 *      @SWG\Property(
 *          property="id",
 *          description="job type id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="grade_id",
 *          description="grade id",
 *          type="integer"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="DatesArrayV1",
 *      required={"date"},
 *      @SWG\Property(
 *          property="date",
 *          description="date",
 *          type="string"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="SegmentsArrayV1",
 *      required={"name", "from", "to"},
 *      @SWG\Property(
 *          property="name",
 *          description="segment name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="from",
 *          description="from",
 *          type="time"
 *      ),
 *      @SWG\Property(
 *          property="to",
 *          description="to",
 *          type="time"
 *      ),
 *      @SWG\Property(
 *          property="inherit_id",
 *          description="segment inherit id",
 *          type="integer"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="SegmentsUpdateArrayV1",
 *      required={"name", "from", "to", "id"},
 *      @SWG\Property(
 *          property="name",
 *          description="segment name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="from",
 *          description="from",
 *          type="time"
 *      ),
 *      @SWG\Property(
 *          property="to",
 *          description="to",
 *          type="time"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="inherit_id",
 *          description="segment inherit id",
 *          type="integer"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="SegmentsUpdateNamesArrayV1",
 *      required={"name", "id"},
 *      @SWG\Property(
 *          property="name",
 *          description="segment name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer"
 *      )
 * )
 *
 * @SWG\Definition (
 *      definition="CarbonObjectV1",
 *      @SWG\Property(
 *          property="date",
 *          description="date time",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timezone_type",
 *          description="timezone type",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="timezone",
 *          description="timezone",
 *          type="string"
 *      )
 * )
 */