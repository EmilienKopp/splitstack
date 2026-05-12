import { User } from "$types/index";
import { page } from "@inertiajs/svelte";
import { get as storeGet } from "svelte/store";

export class AppUser implements Omit<User, "password"> {
  id: number;
  email: string;
  handle: string;
  first_name: string;
  last_name: string;

  constructor(user: User) {
    if (!user) {
      throw new Error("User object is required to create an AppUser instance.");
    }
    this.id = user.id;
    this.email = user.email;
    this.handle = user.handle;
    this.first_name = user.first_name;
    this.last_name = user.last_name;
  }

  static get() {
    return new AppUser(storeGet(page).props.auth.user);
  }

  get name() {
    return `${this.first_name} ${this.last_name}`;
  }

  get shortName() {
    return `${this.first_name.charAt(0)}. ${this.last_name}`;
  }

  get initials() {
    return `${this.first_name.charAt(0)}.${this.last_name.charAt(0)}.`;
  }
}

type AppUserProperty = keyof AppUser | "name" | "shortName" | "initials";

/**
 * Global utility function to get the current user object or a property from it.
 * @example appUser() // returns the user object
 * @example appUser('initials') // returns the initials of the user
 * @param property The property to get from the user object. If not provided, the whole user object is returned.
 * @returns either the user object or the requested property from the user object.
 */
export function appUser(property?: AppUserProperty): AppUser | string | number {
  const user = AppUser.get();
  if (!property || !user) return user;

  // Find an eventual getter for that property
  if (!(property in user)) {
    const getter = Object.getOwnPropertyDescriptor(
      AppUser.prototype,
      property
    )?.get;
    if (getter) return getter.call(user);
  }

  return user[property];
}
