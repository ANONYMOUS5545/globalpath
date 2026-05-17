import type { AccessTier, AppUser, MembershipType } from "./types";

const rank: Record<MembershipType | AccessTier, number> = {
  FREE: 0,
  PREMIUM: 1,
  PREMIUM_PLUS: 2
};

export function canAccessTier(user: AppUser | null, required: AccessTier) {
  if (required === "FREE") return true;
  if (!user) return false;
  return rank[user.membershipType] >= rank[required];
}

export function accessibleTiers(user: AppUser | null): AccessTier[] {
  if (!user) return ["FREE"];
  if (user.membershipType === "PREMIUM_PLUS") return ["FREE", "PREMIUM", "PREMIUM_PLUS"];
  if (user.membershipType === "PREMIUM") return ["FREE", "PREMIUM"];
  return ["FREE"];
}

export function upgradeTarget(required: AccessTier) {
  return required === "PREMIUM_PLUS" ? "/membership#premium-plus" : "/membership#premium";
}
