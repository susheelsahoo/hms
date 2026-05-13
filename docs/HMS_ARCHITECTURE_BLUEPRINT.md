# Hotel Management SaaS Architecture Blueprint

## 1. Architecture Positioning

This HMS should begin as a modular monolith: one Laravel 13 application, one deployable unit, clear module boundaries, PostgreSQL as the system of record, Redis for cache/queues/realtime coordination, and events for module-to-module communication.

The goal is to make every module independently understandable while avoiding the operational cost of microservices too early. Modules should communicate through contracts, actions, DTOs, events, listeners, and read-only queries. Avoid reaching into another module's internals.

## 2. Enterprise Folder Structure

```text
app/
  Console/
    Commands/
  Exceptions/
    Handler.php
  Http/
    Kernel.php
    Middleware/
      Authenticate.php
      EnsureTenantResolved.php
      EnsureHotelAccess.php
      EnsureRole.php
      ForceJsonResponse.php
      RequestId.php
    Resources/
  Providers/
    AppServiceProvider.php
    EventServiceProvider.php
    RouteServiceProvider.php
    ModuleServiceProvider.php
  Support/
    Context/
      TenantContext.php
      HotelContext.php
      UserContext.php
    Pipeline/
    Security/
    Tenancy/

Modules/
  Auth/
  Organization/
  User/
  Hotel/
  Room/
  Booking/
  Payment/
  Invoice/
  Guest/
  Notification/
  Report/
  Subscription/
  Audit/
  Shared/

bootstrap/
  app.php
  providers.php
  cache/

config/
  app.php
  auth.php
  cache.php
  database.php
  hms.php
  modules.php
  queue.php
  redis.php
  services.php
  tenancy.php

database/
  factories/
  migrations/
    landlord/
    tenant/
    shared/
  seeders/
    DatabaseSeeder.php
    RoleSeeder.php
    DemoTenantSeeder.php

routes/
  api.php
  web.php
  console.php
  channels.php
  api/
    v1.php
    v1/
      auth.php
      super-admin.php
      hotel-admin.php
      hotel-manager.php

storage/
  app/
  framework/
  logs/

tests/
  Feature/
  Unit/
  Integration/
  Architecture/
  Modules/
```

## 3. Standard Module Structure

Every domain module should follow the same shape:

```text
Modules/Booking/
  Actions/
    CreateBookingAction.php
    CancelBookingAction.php
    CheckInBookingAction.php
    CheckOutBookingAction.php
  Controllers/
    Api/
      V1/
        BookingController.php
  DTOs/
    CreateBookingDTO.php
    CancelBookingDTO.php
  Enums/
    BookingStatus.php
    PaymentStatus.php
  Events/
    BookingCreated.php
    BookingCancelled.php
    BookingCheckedIn.php
  Exceptions/
    BookingConflictException.php
    RoomUnavailableException.php
  Interfaces/
    BookingRepositoryInterface.php
    BookingNumberGeneratorInterface.php
  Jobs/
    ExpirePendingBookingJob.php
  Listeners/
    ReserveBookedRooms.php
    SendBookingConfirmation.php
    WriteBookingAuditLog.php
  Mail/
    BookingConfirmationMail.php
  Models/
    Booking.php
    BookingRoom.php
  Notifications/
    BookingCreatedNotification.php
  Observers/
    BookingObserver.php
  Policies/
    BookingPolicy.php
  Providers/
    BookingServiceProvider.php
    BookingEventServiceProvider.php
  Repositories/
    EloquentBookingRepository.php
  Requests/
    CreateBookingRequest.php
    UpdateBookingRequest.php
  Resources/
    BookingResource.php
    BookingCollection.php
  Routes/
    api.php
  Services/
    BookingService.php
    AvailabilityService.php
  Traits/
    GeneratesBookingNumbers.php
  Tests/
    Feature/
    Unit/
```

Use the same structure for Auth, Organization, User, Hotel, Room, Payment, Invoice, Guest, Notification, Report, Subscription, and Audit.

## 4. Shared Module Structure

```text
Modules/Shared/
  Actions/
  Contracts/
    Auditable.php
    TenantScoped.php
    HotelScoped.php
    RepositoryInterface.php
  DTOs/
    PaginationDTO.php
    MoneyDTO.php
    DateRangeDTO.php
  Enums/
    Currency.php
    UserStatus.php
    RecordStatus.php
  Events/
    DomainEvent.php
  Exceptions/
    DomainException.php
    AuthorizationException.php
    TenantNotResolvedException.php
    HotelAccessDeniedException.php
  Http/
    ApiResponse.php
  Models/
    BaseModel.php
    TenantModel.php
    HotelScopedModel.php
  Providers/
    SharedServiceProvider.php
  Repositories/
    BaseEloquentRepository.php
  Services/
    Clock.php
    IdGenerator.php
  Traits/
    BelongsToOrganization.php
    BelongsToHotel.php
    HasMetadata.php
    HasStatus.php
    UsesUuid.php
  ValueObjects/
    Money.php
    DateRange.php
    PhoneNumber.php
```

## 5. Role-Based Module Layout

Super Admin features:

```text
Modules/SuperAdmin/
  Controllers/Api/V1/
    OrganizationsController.php
    HotelsController.php
    SubscriptionsController.php
    GlobalAnalyticsController.php
    UsersController.php
    BillingController.php
  Services/
    OrganizationManagementService.php
    GlobalAnalyticsService.php
    SaaSBillingService.php
  Policies/
```

Hotel Admin features:

```text
Modules/HotelAdmin/
  Controllers/Api/V1/
    HotelsController.php
    RoomsController.php
    StaffController.php
    BookingsController.php
    ReportsController.php
    PricingController.php
  Services/
    StaffManagementService.php
    PricingService.php
```

Hotel Manager features:

```text
Modules/HotelManager/
  Controllers/Api/V1/
    BookingOperationsController.php
    CheckInController.php
    CheckOutController.php
    GuestsController.php
    RoomAvailabilityController.php
    DailyOperationsController.php
  Services/
    FrontDeskService.php
    DailyOperationsService.php
```

These role modules should orchestrate use cases from domain modules. They should not own core entities such as Booking, Room, Hotel, or Payment.

## 6. Route Organization and API Versioning

```text
routes/api.php
routes/api/v1.php
routes/api/v1/auth.php
routes/api/v1/super-admin.php
routes/api/v1/hotel-admin.php
routes/api/v1/hotel-manager.php
Modules/Booking/Routes/api.php
Modules/Payment/Routes/api.php
Modules/Room/Routes/api.php
```

Recommended route groups:

```php
Route::prefix('v1')
    ->middleware(['api', 'auth:sanctum', 'request.id', 'tenant.resolve'])
    ->group(function (): void {
        require __DIR__.'/api/v1/super-admin.php';
        require __DIR__.'/api/v1/hotel-admin.php';
        require __DIR__.'/api/v1/hotel-manager.php';
    });
```

Use route names like `api.v1.bookings.store`, `api.v1.hotels.rooms.index`, and `api.v1.payments.capture`.

## 7. Example Booking Module Classes

`BookingController`:

```php
final class BookingController
{
    public function __construct(private readonly BookingService $bookings) {}

    public function store(CreateBookingRequest $request): BookingResource
    {
        $booking = $this->bookings->create(CreateBookingDTO::fromRequest($request));

        return BookingResource::make($booking);
    }
}
```

`CreateBookingRequest`:

```php
final class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Booking::class, $this->route('hotel')]);
    }

    public function rules(): array
    {
        return [
            'guest_id' => ['required', 'integer', 'exists:guests,id'],
            'room_ids' => ['required', 'array', 'min:1'],
            'room_ids.*' => ['integer', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'special_requests' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
```

`CreateBookingDTO`:

```php
final readonly class CreateBookingDTO
{
    public function __construct(
        public int $organizationId,
        public int $hotelId,
        public int $guestId,
        public array $roomIds,
        public CarbonImmutable $checkInDate,
        public CarbonImmutable $checkOutDate,
        public ?string $specialRequests,
        public int $createdBy,
    ) {}
}
```

`BookingService`:

```php
final class BookingService
{
    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly AvailabilityService $availability,
    ) {}

    public function create(CreateBookingDTO $dto): Booking
    {
        return DB::transaction(function () use ($dto): Booking {
            $this->availability->lockAvailableRooms(
                hotelId: $dto->hotelId,
                roomIds: $dto->roomIds,
                checkInDate: $dto->checkInDate,
                checkOutDate: $dto->checkOutDate,
            );

            $booking = $this->bookings->createFromDTO($dto);

            BookingCreated::dispatch($booking->id);

            return $booking;
        }, attempts: 3);
    }
}
```

`BookingRepository`:

```php
interface BookingRepositoryInterface
{
    public function createFromDTO(CreateBookingDTO $dto): Booking;
    public function findForHotel(int $hotelId, int $bookingId): ?Booking;
}
```

`BookingCreated` event:

```php
final readonly class BookingCreated
{
    public function __construct(public int $bookingId) {}
}
```

`SendBookingConfirmation` listener:

```php
final class SendBookingConfirmation implements ShouldQueue
{
    public function handle(BookingCreated $event): void
    {
        $booking = Booking::query()->with(['guest', 'hotel', 'rooms'])->findOrFail($event->bookingId);

        Notification::send($booking->guest, new BookingCreatedNotification($booking));
    }
}
```

`BookingPolicy`:

```php
final class BookingPolicy
{
    public function create(User $user, Hotel $hotel): bool
    {
        return $user->isSuperAdmin()
            || $user->hotels()->whereKey($hotel->id)->wherePivotIn('access_type', ['owner', 'admin', 'manager'])->exists();
    }
}
```

## 8. Example Payment Module

```text
Modules/Payment/
  Actions/
    CapturePaymentAction.php
    RefundPaymentAction.php
  DTOs/
    CapturePaymentDTO.php
    RefundPaymentDTO.php
  Events/
    PaymentSucceeded.php
    PaymentFailed.php
    PaymentRefunded.php
  Gateways/
    PaymentGatewayInterface.php
    StripePaymentGateway.php
    RazorpayPaymentGateway.php
  Jobs/
    ReconcilePaymentJob.php
  Listeners/
    MarkBookingAsPaid.php
    GenerateInvoiceAfterPayment.php
  Models/
    Payment.php
  Repositories/
    EloquentPaymentRepository.php
  Services/
    PaymentService.php
    PaymentReconciliationService.php
```

Payments should be idempotent. Store gateway transaction IDs and use unique or partial unique indexes after finalizing gateway rules.

## 9. Notification Flow

1. Domain event occurs, such as `BookingCreated`.
2. Listener builds notification payload.
3. Notification is persisted in `notifications`.
4. Job sends email/SMS/push.
5. Realtime event broadcasts to hotel dashboard.
6. Audit log is written asynchronously.

Channels: database, mail, SMS provider, push, WebSocket broadcast.

## 10. Event-Driven Workflows

Booking Created:

```text
CreateBookingAction
  -> DB transaction
  -> lock rooms
  -> create booking
  -> create booking_rooms
  -> dispatch BookingCreated
BookingCreated listeners
  -> SendBookingConfirmation
  -> NotifyHotelDashboard
  -> WriteAuditLog
  -> SchedulePaymentReminder
```

Payment Success:

```text
PaymentGatewayWebhook
  -> VerifySignature
  -> idempotency check
  -> record payment
  -> dispatch PaymentSucceeded
PaymentSucceeded listeners
  -> UpdateBookingPaymentStatus
  -> GenerateInvoice
  -> SendPaymentReceipt
  -> WriteAuditLog
```

Booking Cancelled:

```text
CancelBookingAction
  -> DB transaction
  -> lock booking
  -> release rooms
  -> update status
  -> dispatch BookingCancelled
BookingCancelled listeners
  -> SendCancellationNotification
  -> TriggerRefundWorkflow
  -> WriteAuditLog
```

Room Status Updated:

```text
UpdateRoomStatusAction
  -> DB transaction
  -> update room status
  -> dispatch RoomStatusUpdated
RoomStatusUpdated listeners
  -> ClearAvailabilityCache
  -> BroadcastRoomStatus
  -> WriteAuditLog
```

## 11. Multi-Tenant Strategy

Use single database, shared tables, and `organization_id` on tenant-owned records initially. This works well for reporting, onboarding speed, and operational simplicity.

Tenant resolution order:

1. Authenticated user context.
2. `X-Organization-ID` header for multi-org super/admin users.
3. Hotel subdomain or slug for public booking flows.
4. Route parameter fallback.

Middleware:

```text
RequestId
ForceJsonResponse
Authenticate
ResolveTenant
EnsureTenantIsActive
EnsureHotelAccess
EnsureRole
```

Tenant context:

```text
TenantContext
  organizationId()
  organization()
  isSuperAdminMode()

HotelContext
  hotelId()
  hotel()
```

Rules:

- Super Admin can bypass tenant scoping only through explicit platform routes.
- Hotel Admin can query assigned hotels in their organization.
- Hotel Manager can access assigned hotel operations only.
- All repositories must apply tenant/hotel scope unless explicitly marked platform-level.

## 12. Authorization Flow

```text
Request
  -> auth middleware
  -> tenant resolution
  -> role middleware for route-level guard
  -> policy for model/action-level authorization
  -> service/action execution
```

Use policies for business authorization and middleware for coarse route access.

## 13. Cache and Redis Strategy

Redis usage:

- Cache tenant, hotel, and role lookup data.
- Queue jobs by domain.
- Store rate limiting counters.
- Store short-lived availability snapshots.
- Broadcast room-status and booking-dashboard events.
- Use distributed locks for rare cross-process coordination.

Recommended keys:

```text
tenant:{organization_id}:settings
hotel:{hotel_id}:settings
hotel:{hotel_id}:room-types
hotel:{hotel_id}:availability:{date}
user:{user_id}:hotel-access
roles:all
dashboard:{hotel_id}:{date}
```

Never trust cache for final room availability. Always verify under a database transaction and row lock.

## 14. Queue Architecture

```text
queues:
  critical       payment webhooks, invoice generation
  bookings       booking confirmation, expiry jobs
  notifications  email, SMS, push
  reports        heavy reporting exports
  audit          audit log writes
  default        non-critical background work
```

Use `ShouldQueue`, job uniqueness where needed, retry backoff, dead-letter monitoring, and idempotency keys for payment and notification jobs.

## 15. Database Transaction and Locking Strategy

Use transactions for booking, payment, invoice, and check-in/check-out flows.

Booking availability should use row-level locking:

```php
Room::query()
    ->where('hotel_id', $hotelId)
    ->whereIn('id', $roomIds)
    ->lockForUpdate()
    ->get();
```

Then check conflicting bookings inside the same transaction. For advanced availability, consider PostgreSQL exclusion constraints with date ranges later.

Use `DB::transaction($callback, attempts: 3)` to retry deadlocks.

## 16. PostgreSQL Optimization

Recommended now:

- Composite indexes beginning with `organization_id` and `hotel_id`.
- Partial indexes for active bookings and available rooms.
- GIN indexes for `jsonb` fields that are actually queried.
- `timestampsTz()` for globally correct auditability.
- `decimal(12,2)` for money.

Add later when traffic grows:

- Partition `audit_logs`, `notifications`, `payments`, and `bookings` by month.
- Partition `bookings` by `hotel_id` only if a few hotels become extremely large.
- Add read replicas for reports.
- Use materialized views for dashboards and occupancy reports.
- Consider PostgreSQL range types for stay-date overlap queries.

## 17. Testing Structure

```text
tests/
  Architecture/
    ModuleBoundaryTest.php
  Feature/
    Api/
      V1/
  Integration/
    BookingWorkflowTest.php
    PaymentWebhookTest.php
  Modules/
    Booking/
      Feature/
      Unit/
    Payment/
      Feature/
      Unit/
  Unit/
```

Test categories:

- Policy and role access tests.
- Tenant scoping tests.
- Booking conflict tests.
- Payment webhook idempotency tests.
- Queue listener tests.
- Repository query tests.

## 18. Naming Conventions

- Module names: singular domain names, PascalCase, such as `Booking`, `Payment`, `Hotel`.
- Controllers: `BookingController`, `RoomAvailabilityController`.
- Actions: verb-first, such as `CreateBookingAction`, `CapturePaymentAction`.
- DTOs: purpose-first, such as `CreateBookingDTO`.
- Events: past tense, such as `BookingCreated`, `PaymentSucceeded`.
- Listeners: imperative, such as `SendBookingConfirmation`.
- Jobs: imperative plus `Job`, such as `ExpirePendingBookingJob`.
- Policies: model plus `Policy`, such as `BookingPolicy`.
- Repositories: interface plus implementation, such as `BookingRepositoryInterface` and `EloquentBookingRepository`.
- Routes: plural resource names, kebab-case URIs.
- Enums: singular concept, such as `BookingStatus`.

## 19. Coding Standards

- PSR-12, strict typing, readonly DTOs, final classes by default for application services.
- Controllers stay thin.
- Business rules live in actions/services.
- Persistence lives behind repositories for complex domains.
- Events cross module boundaries.
- Avoid static facades inside domain logic when dependency injection is reasonable.
- Validate with Form Requests.
- Return API Resources, not raw models.
- Never leak payment gateway payloads directly to clients.

## 20. Future Microservice Migration Strategy

Start modular monolith because it gives fast delivery, local transactions, simpler deployment, easier debugging, and lower infrastructure cost.

Split only when there is clear pressure:

- Independent scaling requirements.
- Separate team ownership.
- Different security/compliance boundary.
- Different release cadence.
- Long-running workloads harming core request performance.

Likely future services:

- Payment service: gateway isolation, PCI/security boundary.
- Notification service: high-volume email/SMS/push.
- Reporting service: heavy analytical workloads.
- Subscription/Billing service: SaaS commercial logic.
- Audit service: high-write immutable logs.
- Availability service: if room search becomes very high traffic.

Keep module contracts event-driven now so extraction later is mostly transport changes, not business rewrites.
